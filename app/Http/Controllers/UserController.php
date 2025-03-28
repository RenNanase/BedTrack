<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Check if the current user is a superadmin
     */
    private function checkSuperAdmin()
    {
        if (!Auth::check()) {
            abort(403, 'User not authenticated.');
        }

        $user = Auth::user();
        Log::info('Checking superadmin access for user:', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ]);

        if ($user->role !== 'superadmin') {
            abort(403, 'Unauthorized action. Superadmin access required.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkSuperAdmin();
        $users = User::with('ward')->get();
        $wards = Ward::all();
        return view('users.index', compact('users', 'wards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkSuperAdmin();
        $wards = Ward::all();
        return view('users.create', compact('wards'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'ward_id' => 'required_if:role,staff|exists:wards,id',
            'role' => 'required|in:admin,staff,superadmin',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'ward_id' => $request->role === 'staff' ? $request->ward_id : null,
            'role' => $request->role,
        ]);

        // If user is staff, attach them to the ward
        if ($request->role === 'staff' && $request->ward_id) {
            $user->wards()->attach($request->ward_id);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->checkSuperAdmin();
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->checkSuperAdmin();
        $wards = Ward::all();
        return view('users.edit', compact('user', 'wards'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'ward_id' => 'required|exists:wards,id',
            'role' => 'required|in:admin,staff,superadmin',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'ward_id' => $request->ward_id,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->checkSuperAdmin();
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
