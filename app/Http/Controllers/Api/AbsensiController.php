<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
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
}
