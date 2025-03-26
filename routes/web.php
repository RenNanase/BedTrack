<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ward Selection Route
Route::middleware('auth')->group(function() {
    Route::get('/select-ward', [WardController::class, 'selectWard'])->name('select.ward');
    Route::post('/select-ward', [WardController::class, 'storeWardSelection']);
});

// Protected Routes
Route::middleware(['auth', 'ward.selection'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Bed Management Routes
    Route::get('/beds/{bed}', [BedController::class, 'show'])->name('beds.show');
    Route::put('/beds/{bed}/status', [BedController::class, 'updateStatus'])->name('beds.update-status');
    Route::get('/beds/{bed}/patient/edit', [BedController::class, 'editPatient'])->name('beds.edit-patient');
    Route::put('/beds/{bed}/patient', [BedController::class, 'updatePatient'])->name('beds.update-patient');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Register Admin Middleware in App\Providers\AppServiceProvider.php boot() method with:
// Route::aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);

Route::get('/activity-logs/load-more', [App\Http\Controllers\ActivityLogController::class, 'loadMore'])->name('activity-logs.load-more');
