<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Room;
use App\Models\Bed;
use Illuminate\Http\Request;

class RoomBedManagementController extends Controller
{
    public function addNurseryCribs(Ward $ward)
    {
        if ($ward->ward_name !== 'Nursery') {
            return redirect()->route('dashboard')->with('error', 'This feature is only available for the Nursery ward.');
        }

        return view('room-management.add-nursery-cribs', compact('ward'));
    }

    public function storeNurseryCribs(Request $request, Ward $ward)
    {
        if ($ward->ward_name !== 'Nursery') {
            return redirect()->route('dashboard')->with('error', 'This feature is only available for the Nursery ward.');
        }

        $request->validate([
            'number_of_cribs' => 'required|integer|min:1|max:50',
        ]);

        $nurseryRoom = $ward->rooms->first();
        $currentCribCount = $nurseryRoom->beds->count();
        $newCribCount = $currentCribCount + $request->number_of_cribs;

        // Update room capacity
        $nurseryRoom->update(['capacity' => $newCribCount]);

        // Add new cribs
        for ($i = $currentCribCount + 1; $i <= $newCribCount; $i++) {
            Bed::create([
                'bed_number' => 'Crib ' . $i,
                'room_id' => $nurseryRoom->id,
                'status' => 'Available',
            ]);
        }

        return redirect()->route('dashboard')->with('success', $request->number_of_cribs . ' cribs added successfully to the nursery room.');
    }

    public function addRoomBeds(Ward $ward)
    {
        if ($ward->ward_name === 'Nursery') {
            return redirect()->route('dashboard')->with('error', 'Please use the nursery cribs management for the Nursery ward.');
        }

        return view('room-management.add-room-beds', compact('ward'));
    }

    public function storeRoomBeds(Request $request, Ward $ward)
    {
        if ($ward->ward_name === 'Nursery') {
            return redirect()->route('dashboard')->with('error', 'Please use the nursery cribs management for the Nursery ward.');
        }

        $request->validate([
            'room_name' => 'required|string|max:255',
            'number_of_beds' => 'required|integer|min:1|max:20',
        ]);

        // Create new room
        $room = Room::create([
            'room_name' => $request->room_name,
            'ward_id' => $ward->id,
            'capacity' => $request->number_of_beds,
        ]);

        // Add beds to the room
        for ($i = 1; $i <= $request->number_of_beds; $i++) {
            Bed::create([
                'bed_number' => 'Bed ' . $i,
                'room_id' => $room->id,
                'status' => 'Available',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'New room with ' . $request->number_of_beds . ' beds added successfully.');
    }
}
