<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DriverLocationController extends Controller
{
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
}
