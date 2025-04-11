<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Ward and Room API Routes
Route::middleware('web')->group(function () {
    Route::get('/wards/{ward}/rooms', function (App\Models\Ward $ward) {
        try {
            $rooms = $ward->rooms()
                ->where('is_blocked', false)
                ->select('id', 'room_name')
                ->get();
            return response()->json($rooms);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load rooms'], 500);
        }
    })->name('api.ward.rooms');

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
    })->name('api.room.available-beds');

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
    })->name('api.room.transfer-out-beds');

    Route::get('/rooms/{room}/available-cribs', function (App\Models\Room $room) {
        try {
            $cribs = $room->beds()
                ->where('status', 'Available')
                ->where('is_crib', true)
                ->select('id', 'bed_number')
                ->get();
            return response()->json($cribs);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load cribs'], 500);
        }
    })->name('api.room.available-cribs');
});
