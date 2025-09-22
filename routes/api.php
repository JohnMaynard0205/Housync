<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RfidController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ESP32 RFID API Routes (with rate limiting for security)
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/rfid/verify', [RfidController::class, 'verifyAccess'])->name('api.rfid.verify');
    Route::post('/rfid-scan', [RfidController::class, 'scanCardDirect'])->name('api.rfid-scan'); // For ESP32Reader.php Activity Logs
    Route::post('/rfid/scan/direct', [RfidController::class, 'scanCardDirect'])->name('api.rfid.scan-direct');
    Route::get('/rfid/latest-uid', [RfidController::class, 'getLatestCardUID'])->name('api.rfid.latest-uid'); // NEW: Get latest UID from ESP32Reader.php
    Route::post('/rfid/generate-uid', [RfidController::class, 'generateCardUID'])->name('api.rfid.generate-uid'); // Fallback: Simple UID generator
    Route::post('/rfid/test', [RfidController::class, 'testConnection'])->name('api.rfid.test');
    Route::get('/system-info', function() {
        return response()->json([
            'success' => true,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_connected' => true,
            'timestamp' => now()->toISOString()
        ]);
    })->name('api.system-info'); // For ESP32Reader.php connection test
});

