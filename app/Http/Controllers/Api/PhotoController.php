<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PhotoController extends Controller
{
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
