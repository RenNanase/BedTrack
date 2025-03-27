<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Room;
use App\Models\Bed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    /**
     * Show the super admin dashboard.
     */
    public function dashboard()
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $wards = Ward::with(['rooms.beds'])->get();
        return view('super-admin.dashboard', compact('wards'));
    }

    /**
     * Show the ward management page.
     */
    public function wardManagement()
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $wards = Ward::with(['rooms.beds'])->get();
        return view('super-admin.ward-management', compact('wards'));
    }

    /**
     * Add new ward.
     */
    public function addWard(Request $request)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'ward_name' => 'required|string|max:255|unique:wards,ward_name',
        ]);

        Ward::create([
            'ward_name' => $request->ward_name,
        ]);

        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Ward added successfully.');
    }

    /**
     * Add new room to a ward.
     */
    public function addRoom(Request $request)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'ward_id' => 'required|exists:wards,id',
            'room_number' => 'required|string|max:255',
            'room_type' => 'required|in:regular,nursery',
        ]);

        Room::create([
            'ward_id' => $request->ward_id,
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
        ]);

        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Room added successfully.');
    }

    /**
     * Add new bed to a room.
     */
    public function addBed(Request $request)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => 'required|string|max:255',
            'bed_type' => 'required|in:regular,crib',
        ]);

        Bed::create([
            'room_id' => $request->room_id,
            'bed_number' => $request->bed_number,
            'bed_type' => $request->bed_type,
            'status' => 'Available',
        ]);

        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Bed added successfully.');
    }

    /**
     * Delete a ward.
     */
    public function deleteWard(Ward $ward)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $ward->delete();
        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Ward deleted successfully.');
    }

    /**
     * Delete a room.
     */
    public function deleteRoom(Room $room)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $room->delete();
        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Room deleted successfully.');
    }

    /**
     * Delete a bed.
     */
    public function deleteBed(Bed $bed)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $bed->delete();
        return redirect()->route('super-admin.ward-management')
            ->with('success', 'Bed deleted successfully.');
    }
}
