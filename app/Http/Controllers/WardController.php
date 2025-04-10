<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatRoom;
use App\Models\User;
use App\Models\Room;
use App\Models\Bed;

class WardController extends Controller
{
    /**
     * Show the ward selection form.
     */
    public function selectWard()
    {
        $user = Auth::user();

        // If user is superadmin or admin, show all wards
        if (in_array($user->role, ['superadmin', 'admin'])) {
            $wards = Ward::all();
        } else {
            // For staff users, only show their assigned wards
            $wards = $user->wards;
        }

        return view('auth.select-ward', compact('wards'));
    }

    /**
     * Store the ward selection.
     */
    public function storeWardSelection(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ward_id' => 'required|exists:wards,id',
        ]);

        // Check if user has access to the selected ward
        if (!in_array($user->role, ['superadmin', 'admin']) &&
            !$user->wards()->where('ward_id', $request->ward_id)->exists()) {
            return back()->with('error', 'You do not have access to this ward.');
        }

        // Store the selected ward in session
        $request->session()->put('selected_ward_id', $request->ward_id);

        // Create or update the user-ward relationship
        $user->wards()->syncWithoutDetaching([$request->ward_id]);

        // Get the selected ward
        $ward = Ward::find($request->ward_id);

        // If it's the nursery ward, redirect to nursery.index
        if ($ward->ward_name === 'Nursery Ward') {
            return redirect()->route('nursery.index');
        }

        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:wards',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:general,nursery,icu'
        ]);

        $ward = Ward::create($request->all());

        // Create a default group chat for the ward
        $chatRoom = ChatRoom::create([
            'name' => "{$ward->name} Group Chat",
            'type' => 'ward',
            'ward_id' => $ward->id
        ]);

        // Get all users in this ward and attach them to the chat
        $users = User::where('ward_id', $ward->id)->get();
        $chatRoom->users()->attach($users->pluck('id'));

        return redirect()->route('ward.index')->with('success', 'Ward created successfully.');
    }

    /**
     * Block a room in the ward.
     */
    public function blockRoom(Request $request, Room $room)
    {
        // Check if user has permission to block rooms in this ward
        if (!auth()->user()->wards->contains($room->ward_id) && !auth()->user()->isAdmin) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You do not have permission to block rooms in this ward.'], 403);
            }
            return back()->with('error', 'You do not have permission to block rooms in this ward.');
        }

        $request->validate([
            'block_remarks' => 'required|string|max:1000',
        ]);

        try {
            $room->update([
                'is_blocked' => true,
                'block_remarks' => $request->block_remarks,
                'blocked_at' => now(),
                'blocked_by' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Room has been blocked successfully.']);
            }
            return back()->with('success', 'Room has been blocked successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to block room: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to block room: ' . $e->getMessage());
        }
    }

    /**
     * Unblock a room in the ward.
     */
    public function unblockRoom(Room $room)
    {
        // Check if user has permission to unblock rooms in this ward
        if (!auth()->user()->wards->contains($room->ward_id) && !auth()->user()->isAdmin) {
            return back()->with('error', 'You do not have permission to unblock rooms in this ward.');
        }

        try {
            $room->update([
                'is_blocked' => false,
                'block_remarks' => null,
                'blocked_at' => null,
                'blocked_by' => null,
            ]);

            return back()->with('success', 'Room has been unblocked successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to unblock room: ' . $e->getMessage());
        }
    }

    public function updateRoomSequence(Request $request)
    {
        if (auth()->user()->role !== 'superadmin') {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        try {
            $rooms = $request->input('rooms');
            
            foreach ($rooms as $roomData) {
                Room::where('id', $roomData['id'])->update([
                    'sequence' => $roomData['sequence']
                ]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function addBed(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => 'required|string|max:10',
            'bed_type' => 'required|in:Regular,Crib'
        ]);

        try {
            $bed = Bed::create([
                'room_id' => $request->room_id,
                'bed_number' => $request->bed_number,
                'bed_type' => $request->bed_type,
                'status' => 'Available'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bed added successfully',
                'bed' => $bed
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add bed: ' . $e->getMessage()
            ], 500);
        }
    }
}
