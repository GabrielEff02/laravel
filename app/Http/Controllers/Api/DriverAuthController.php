<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Driver;
use App\Models\Absen;

class DriverAuthController extends Controller
{
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
}
