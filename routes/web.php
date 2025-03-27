<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\NurseryWardController;
use App\Http\Controllers\RoomBedManagementController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

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

// Super Admin Routes
Route::middleware(['auth', 'super.admin'])->group(function () {
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');
    Route::get('/super-admin/ward-management', [SuperAdminController::class, 'wardManagement'])->name('super-admin.ward-management');
    Route::post('/super-admin/ward', [SuperAdminController::class, 'addWard'])->name('super-admin.add-ward');
    Route::delete('/super-admin/ward/{ward}', [SuperAdminController::class, 'deleteWard'])->name('super-admin.delete-ward');
    Route::post('/super-admin/room', [SuperAdminController::class, 'addRoom'])->name('super-admin.add-room');
    Route::delete('/super-admin/room/{room}', [SuperAdminController::class, 'deleteRoom'])->name('super-admin.delete-room');
    Route::post('/super-admin/bed', [SuperAdminController::class, 'addBed'])->name('super-admin.add-bed');
    Route::delete('/super-admin/bed/{bed}', [SuperAdminController::class, 'deleteBed'])->name('super-admin.delete-bed');
});

// Register Admin Middleware in App\Providers\AppServiceProvider.php boot() method with:
// Route::aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);

Route::get('/activity-logs/load-more', [App\Http\Controllers\ActivityLogController::class, 'loadMore'])->name('activity-logs.load-more');

Route::get('/nursery', [NurseryWardController::class, 'index'])->name('nursery.index');

// Room and Bed Management Routes
Route::get('/ward/{ward}/add-nursery-cribs', [RoomBedManagementController::class, 'addNurseryCribs'])->name('room-management.add-nursery-cribs');
Route::post('/ward/{ward}/store-nursery-cribs', [RoomBedManagementController::class, 'storeNurseryCribs'])->name('room-management.store-nursery-cribs');
Route::get('/ward/{ward}/add-room-beds', [RoomBedManagementController::class, 'addRoomBeds'])->name('room-management.add-room-beds');
Route::post('/ward/{ward}/store-room-beds', [RoomBedManagementController::class, 'storeRoomBeds'])->name('room-management.store-room-beds');

// Ward and Room Routes for Transfer
Route::get('/wards/{ward}/rooms', function (App\Models\Ward $ward) {
    try {
        Log::info('Fetching rooms for ward: ' . $ward->id);
        $rooms = $ward->rooms()->select('id', 'room_name')->get();
        Log::info('Found rooms: ' . $rooms->count());
        return response()->json($rooms);
    } catch (\Exception $e) {
        Log::error('Error fetching rooms for ward: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to load rooms'], 500);
    }
})->name('ward.rooms');

Route::get('/rooms/{room}/available-beds', function (App\Models\Room $room) {
    try {
        Log::info('Fetching available beds for room: ' . $room->id);
        $beds = $room->beds()
            ->where('status', 'Available')
            ->select('id', 'bed_number')
            ->get();
        Log::info('Found available beds: ' . $beds->count());
        return response()->json($beds);
    } catch (\Exception $e) {
        Log::error('Error fetching available beds for room: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to load beds'], 500);
    }
})->name('room.available-beds');

Route::get('/rooms/{room}/transfer-out-beds', function (App\Models\Room $room) {
    try {
        Log::info('Fetching transfer-out beds for room: ' . $room->id);
        $beds = $room->beds()
            ->where('status', 'Transfer-out')
            ->select('id', 'bed_number')
            ->get();
        Log::info('Found transfer-out beds: ' . $beds->count());
        return response()->json($beds);
    } catch (\Exception $e) {
        Log::error('Error fetching transfer-out beds for room: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to load beds'], 500);
    }
})->name('room.transfer-out-beds');

Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
