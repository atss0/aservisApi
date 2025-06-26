<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\RideLocationController;
use App\Http\Controllers\RideStudentLogController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ParentController;

// 🔓 Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/routes', function () {
    return response()->json(['routes' => \App\Models\Route::all()]);
});
// ✅ Yönetimsel İşlemler (admin panelden yapılması önerilir ama istekle ekledin)
Route::get('/students', [StudentController::class, 'index']);
Route::post('/students', [StudentController::class, 'store']);
Route::get('/vehicles', [VehicleController::class, 'getAllVehicles']);
Route::post('/vehicles', [VehicleController::class, 'store']);
Route::post('/vehicles/{vehicle_id}/students', [VehicleController::class, 'assignStudents']);
// 🔐 Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {

    // ✅ Ortak Kullanıcı İşlemleri
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // 🚗 Şoför (Driver) işlemleri
    Route::middleware('role:driver')->group(function () {
        // Servis kontrol
        Route::post('/rides/start', [RideController::class, 'startRide']);
        Route::post('/rides/{id}/end', [RideController::class, 'endRide']);

        // Konum gönderimi
        Route::post('/rides/{id}/location', [RideLocationController::class, 'sendLocation']);

        // Öğrenci bindi/indi işlemleri
        Route::post('/rides/{ride_id}/student/{student_id}/pickup', [RideStudentLogController::class, 'pickupStudent']);
        Route::post('/rides/{ride_id}/student/{student_id}/dropoff', [RideStudentLogController::class, 'dropoffStudent']);

        // Şoföre tanımlı öğrenciler
        Route::get('/driver/students', [RideController::class, 'getDriverStudents']);
        Route::get('/driver/rides', [RideController::class, 'driverRideHistory']);

        Route::get('/driver/vehicles', [VehicleController::class, 'driverVehicles']);
    });

    Route::middleware(['auth:sanctum', 'role:parent'])->group(function () {
        Route::get('/parent/children', [ParentController::class, 'getChildren']);
        Route::get('/parent/child/{id}/status', [ParentController::class, 'getChildStatus']);
        Route::get('/parent/child/{id}/rides', [ParentController::class, 'childRideHistory']);
    });
});