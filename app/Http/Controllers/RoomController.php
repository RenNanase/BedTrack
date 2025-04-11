<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function getAvailableCribs(Room $room)
    {
        try {
            Log::info('Fetching available cribs for room: ' . $room->id);
            $cribs = $room->cribs()
                ->where('status', 'available')
                ->select('id', 'crib_number')
                ->get();
            Log::info('Found available cribs: ' . $cribs->count());
            return response()->json($cribs);
        } catch (\Exception $e) {
            Log::error('Error fetching available cribs: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load available cribs'], 500);
        }
    }
} 