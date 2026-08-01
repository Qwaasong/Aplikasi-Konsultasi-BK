<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\Akpd;
use App\Models\Dcm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Endpoint publik (tidak butuh token).
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Endpoint terproteksi (Bearer token dari OAuth password grant).
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    // Contoh data terproteksi untuk membuktikan guard auth:api bekerja.
    Route::get('asesmen', function (Request $request) {
        return response()->json([
            'status' => true,
            'data' => [
                'akpd' => Akpd::count(),
                'dcm' => Dcm::count(),
            ],
        ], 200);
    });
});
