<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Driver;
use App\Models\Absen;

class DeliveryController extends Controller
{
    public function absen(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'lat' => 'required|numeric',
            'lang' => 'required|numeric',
            'kodep' => 'required',
            'namap' => 'required',
            'check_in' => 'required|boolean',
            'photo' => 'nullable|image|max:2048'
        ]);

        $code = $request->code;
        $lat = $request->lat;
        $lng = $request->lang;
        $kodep = $request->kodep;
        $namap = $request->namap;
        $checkIn = $request->check_in;

        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $jam = Carbon::now('Asia/Jakarta')->toTimeString();

        $lokasiPerusahaan = DB::table('compan')->where('code', $code)->first();

        if (!$lokasiPerusahaan) {
            return response()->json(['message' => 'QR Code tidak valid', 'error' => true]);
        }

        $distance = $this->haversineDistance($lokasiPerusahaan->lat, $lokasiPerusahaan->lang, $lat, $lng);
        if ($distance > 1) {
            return response()->json(['message' => 'Jarak terlalu jauh', 'error' => true]);
        }

        $folder = $checkIn ? 'check in/' : 'check out/';
        $fileName = $kodep . str_replace('-', '', $today) . 'T' . str_replace(':', '', $jam) . '.jpg';
        $path = "images/absen/$folder$fileName";

        if ($request->hasFile('photo')) {
            $request->file('photo')->move(public_path("images/absen/$folder"), $fileName);
        }

        if ($checkIn) {
            DB::table('absen')->insert([
                'kodep' => $kodep,
                'namap' => $namap,
                'tgl' => $today,
                'jam_in' => $jam,
                'image_in' => "$folder$fileName"
            ]);
            return response()->json(['message' => 'Lokasi sesuai, Check In berhasil', 'error' => false]);
        } else {
            DB::table('absen')
                ->where('kodep', $kodep)
                ->where('namap', $namap)
                ->where('tgl', $today)
                ->update([
                    'jam_out' => $jam,
                    'image_out' => "$folder$fileName",
                    'waktu' => DB::raw("TIMEDIFF('$jam', jam_in)")
                ]);
            return response()->json(['message' => 'Lokasi sesuai, Check Out berhasil', 'error' => false]);
        }
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
    public function getDeliveryData(Request $request)
    {
        $driverId = $request->query('driverId');
        $is_delivered = $request->query('is_delivered');
        $username = $request->query('username');

        // Driver Data
        $driverData = DB::table('drivers')
            ->select('driver_id', 'driver_name', 'region')
            ->where('manager', 0)
            ->get();

        // Undelivered Count Data
        $undeliveredCountData = 0;
        if ($driverId) {
            $undeliveredCountData = DB::table('transactions')
                ->where('status', '1')
                ->where('is_delivery', '1')
                ->distinct('username')
                ->count('username');
        }

        // Delivered / Undelivered Customers
        $undeliveredCusData = [];
        $deliveredCusData = [];

        if (!is_null($is_delivered)) {
            $customers = DB::table('userd as u')
                ->join('transactions as t', 'u.username', '=', 't.username')
                ->select('u.nama', 'u.alamat', 'u.username')
                ->where('t.status', $is_delivered)
                ->where('t.is_delivery', '1')
                ->distinct()
                ->get();

            if ($is_delivered === "1") {
                $undeliveredCusData = $customers;
            } elseif ($is_delivered === "2") {
                $deliveredCusData = $customers;
            }
        }

        // Transaction Details
        $transactionDetailData = [];
        if ($username) {
            $transactionDetailData = DB::table('transaction_items as ti')
                ->join('products as p', 'ti.product_id', '=', 'p.product_id')
                ->join('transactions as t', 'ti.transaction_id', '=', 't.transaction_id')
                ->select('p.product_name', DB::raw('SUM(ti.quantity) as quantity'))
                ->where('t.username', $username)
                ->where('t.status', '1')
                ->where('t.is_delivery', '1')
                ->groupBy('ti.product_id', 'p.product_name')
                ->get();
        }

        return response()->json([
            'driver data' => $driverData,
            'undelivered count data' => $undeliveredCountData,
            'undelivered customer data' => $undeliveredCusData,
            'delivered customer data' => $deliveredCusData,
            'transaction detail' => $transactionDetailData
        ]);
    }

    public function startDelivery(Request $request)
    {
        $request->validate([
            'username' => 'required|string'
        ]);

        $username = $request->input('username');

        try {
            // Cek apakah masih ada pengantaran status = 2
            $existingDelivery = DB::table('transactions')
                ->where('status', '2')
                ->where('is_delivery', '1')
                ->exists();

            if ($existingDelivery) {
                return response()->json([
                    'error' => true,
                    'message' => 'Selesaikan pengantaran sebelumnya terlebih dahulu'
                ]);
            }

            // Update pengantaran baru untuk username
            $updated = DB::table('transactions')
                ->where('username', $username)
                ->where('status', '1')
                ->where('is_delivery', '1')
                ->update(['status' => '2']);

            if ($updated > 0) {
                return response()->json([
                    'error' => false,
                    'message' => 'Delivery on Progress!'
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Tidak ada transaksi yang diperbarui'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function finishDelivery(Request $request)
    {
        $request->validate([
            'username' => 'required|string'
        ]);

        $username = $request->input('username');

        try {
            // Update transaksi yang sedang dikirim menjadi selesai
            $updated = DB::table('transactions')
                ->where('username', $username)
                ->where('status', '2')
                ->where('is_delivery', '1')
                ->update(['status' => '3']);

            if ($updated > 0) {
                return response()->json([
                    'error' => false,
                    'message' => 'Update berhasil'
                ]);
            } else {
                // Debug: cek apakah ada data yang cocok
                $exists = DB::table('transactions')
                    ->where('username', $username)
                    ->where('status', '2')
                    ->where('is_delivery', '1')
                    ->exists();

                return response()->json([
                    'error' => true,
                    'message' => 'Ubah user gagal',
                    'debug' => $exists ? 'Query berhasil tapi tidak ada baris diubah' : 'Tidak ada data dengan username tersebut atau status bukan 2'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    public function login(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header('Content-Type: application/json');
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Username and password are required'
            ]);
        }

        $username = $request->username;
        $password = $request->password;

        $driver = Driver::where('username', $username)->first();

        if (!$driver) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid username or password'
            ]);
        }

        if ($driver->password !== md5($password)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid password'
            ]);
        }

        $tgl = Carbon::now('Asia/Jakarta')->toDateString();
        $checkAbsen = Absen::where('kodep', $username)->where('tgl', $tgl)->exists();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'driver_id' => $driver->driver_id,
            'manager' => $driver->manager,
            'name' => $driver->driver_name,
            'check_in' => $checkAbsen
        ]);
    }
    public function getTodayLocations(Request $request)
    {
        $locations = DB::table('driver_location as dl')
            ->join('drivers as d', 'd.driver_id', '=', 'dl.driver_id')
            ->select('dl.driver_id', 'd.driver_name', 'dl.latitude', 'dl.longitude', 'dl.timestamp')
            ->whereDate('dl.timestamp', now()->toDateString())
            ->where('d.manager', 0)
            ->orderBy('dl.timestamp')
            ->get();

        if ($locations->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data ditemukan']);
        }

        return response()->json($locations);
    }
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|integer|exists:drivers,driver_id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            // Update lokasi terakhir driver
            DB::table('drivers')
                ->where('driver_id', $validated['driver_id'])
                ->update([
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude']
                ]);

            // Tambahkan log lokasi baru ke driver_location
            DB::table('driver_location')->insert([
                'driver_id' => $validated['driver_id'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'timestamp' => now()
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Update and logging successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Update failed: ' . $e->getMessage()
            ]);
        }
    }
    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', // max 5MB
            'driver_username' => 'required|string',
            'user_username' => 'required|string',
        ]);

        try {
            // Simpan file ke folder public/images dan dapatkan path-nya
            $path = $request->file('photo')->store('images', 'public');

            // Simpan informasi ke database
            DB::table('driver_photos')->insert([
                'driver_username' => $request->driver_username,
                'user_username' => $request->user_username,
                'photo_path' => '/' . $path, // Simpan dengan slash di awal seperti versi native
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'photo_path' => '/' . $path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Upload failed: ' . $e->getMessage()
            ]);
        }
    }
}
