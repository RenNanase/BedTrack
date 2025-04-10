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
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\BassinetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

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
    Route::get('/nursery', [NurseryWardController::class, 'index'])->name('nursery.index');

    // Bed Management Routes
    Route::get('/beds/{bed}', [BedController::class, 'show'])->name('beds.show');
    Route::put('/beds/{bed}/status', [BedController::class, 'updateStatus'])->name('beds.update-status');
    Route::get('/beds/{bed}/patient/edit', [BedController::class, 'editPatient'])->name('beds.edit-patient');
    Route::put('/beds/{bed}/patient', [BedController::class, 'updatePatient'])->name('beds.update-patient');

    // Room blocking routes
    Route::post('/rooms/{room}/block', [WardController::class, 'blockRoom'])->name('rooms.block');
    Route::post('/rooms/{room}/unblock', [WardController::class, 'unblockRoom'])->name('rooms.unblock');
});

// Admin Routes (no ward selection required)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Super Admin Routes (no ward selection required)
Route::middleware(['auth'])->group(function () {
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');
    Route::get('/super-admin/ward-management', [SuperAdminController::class, 'wardManagement'])->name('super-admin.ward-management');
    Route::post('/super-admin/ward', [SuperAdminController::class, 'addWard'])->name('super-admin.add-ward');
    Route::delete('/super-admin/ward/{ward}', [SuperAdminController::class, 'deleteWard'])->name('super-admin.delete-ward');
    Route::post('/super-admin/room', [SuperAdminController::class, 'addRoom'])->name('super-admin.add-room');
    Route::delete('/super-admin/room/{room}', [SuperAdminController::class, 'deleteRoom'])->name('super-admin.delete-room');
    Route::post('/super-admin/bed', [SuperAdminController::class, 'addBed'])->name('super-admin.add-bed');
    Route::delete('/super-admin/bed/{bed}', [SuperAdminController::class, 'deleteBed'])->name('super-admin.delete-bed');
    Route::resource('users', UserController::class);
    Route::post('/add-bed', [WardController::class, 'addBed'])->name('add-bed');
    Route::post('/bassinets', [BassinetController::class, 'store'])->name('super-admin.add-bassinet');
    Route::delete('/bassinets/{bassinet}', [BassinetController::class, 'destroy'])->name('super-admin.delete-bassinet');
});

// Register Admin Middleware in App\Providers\AppServiceProvider.php boot() method with:
// Route::aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);

Route::get('/activity-logs/load-more', [App\Http\Controllers\ActivityLogController::class, 'loadMore'])->name('activity-logs.load-more');

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
            ->whereHas('room', function($query) {
                $query->where('is_blocked', false);
            })
            ->whereHas('room.ward', function($query) {
                $query->where('is_blocked', false);
            })
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
            ->whereHas('room', function($query) {
                $query->where('is_blocked', false);
            })
            ->whereHas('room.ward', function($query) {
                $query->where('is_blocked', false);
            })
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

// Chat Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{chatRoom}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/messages', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{chatRoom}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::put('/chat/{chatRoom}/name', [ChatController::class, 'updateName'])->name('chat.update.name');
    Route::post('/chat/typing', [ChatController::class, 'typing'])->name('chat.typing');
    Route::post('/chat-rooms/{chatRoom}/messages', [ChatMessageController::class, 'store'])->name('chat-messages.store');
    Route::post('/chat-messages/{message}/reply', [ChatMessageController::class, 'reply'])->name('chat-messages.reply');
});

// Test route for Pusher integration
Route::get('/test-pusher', function () {
    try {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
                'debug' => true,
            ]
        );
        
        $data = ['message' => 'This is a test message from the server', 'time' => now()->toDateTimeString()];
        
        // Send to a test channel
        $pusher->trigger('test-channel', 'test-event', $data);
        
        return [
            'success' => true, 
            'message' => 'Test event sent to Pusher', 
            'data' => $data,
            'pusher_config' => [
                'key' => env('PUSHER_APP_KEY'),
                'cluster' => env('PUSHER_APP_CLUSTER')
            ]
        ];
    } catch (\Exception $e) {
        \Log::error('Test Pusher error: ' . $e->getMessage(), ['exception' => $e]);
        return [
            'success' => false, 
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
});

// Debug route for Pusher
Route::get('/debug-pusher', function () {
    return view('debug-pusher');
});

// Room sequence management
Route::post('/rooms/update-sequence', [WardController::class, 'updateRoomSequence'])->name('super-admin.update-room-sequence');

// Bassinet routes
Route::get('/bassinets/{bassinet}', [BassinetController::class, 'show'])->name('bassinets.show');
Route::post('/bassinets/{bassinet}/transfer', [BassinetController::class, 'transfer'])->name('bassinets.transfer');
Route::post('/bassinets', [BassinetController::class, 'store'])->name('bassinets.store');
