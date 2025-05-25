<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
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
}
