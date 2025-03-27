<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Room;
use App\Models\Bed;
use Illuminate\Http\Request;

class NurseryWardController extends Controller
{
    public function index()
    {
        // Get the nursery ward
        $nurseryWard = Ward::where('ward_name', 'Nursery')->first();

        if (!$nurseryWard) {
            return redirect()->route('dashboard')->with('error', 'Nursery ward not found.');
        }

        // Get the nursery room
        $nurseryRoom = Room::where('ward_id', $nurseryWard->id)
                            ->where('room_name', 'Nursery Room')
                            ->first();

        if (!$nurseryRoom) {
            return redirect()->route('dashboard')->with('error', 'Nursery room not found.');
        }

        // Get all cribs in the nursery room
        $cribs = Bed::where('room_id', $nurseryRoom->id)
                    ->orderBy('bed_number')
                    ->get();

        return view('nursery.index', compact('cribs'));
    }
}
