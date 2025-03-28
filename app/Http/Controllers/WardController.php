<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatRoom;
use App\Models\User;

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
}
