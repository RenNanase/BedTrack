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
            'role' => $user->role
        ]);

        if ($user->role !== 'superadmin') {
            abort(403, 'Unauthorized action. Superadmin access required.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkSuperAdmin();
        
        $query = User::with('wards');
        
        // Handle search by username if provided
        if ($request->has('search')) {
            $searchTerm = $request->search;
            Log::info('Searching for users with name like: ' . $searchTerm);
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        
        // Paginate with 9 users per page
        $users = $query->paginate(9);
        $wards = Ward::all();
        
        // If search returns just one user, redirect to edit page
        if ($request->has('search') && $users->count() === 1) {
            Log::info('Found exactly one user, redirecting to edit page for user ID: ' . $users->first()->id);
            return redirect()->route('users.edit', $users->first());
        }
        
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
            'name' => 'required|string|max:255|unique:users,name',
            'password' => 'required|string|min:6|confirmed',
            'ward_id' => 'nullable|exists:wards,id',
            'role' => 'required|in:admin,staff,superadmin',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'ward_id' => $request->ward_id,
            'role' => $request->role,
        ]);

        // If ward_id is provided, attach the user to the ward
        if ($request->ward_id) {
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
        Log::info('Accessing edit user form for user ID: ' . $user->id);
        $user->load('wards');
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
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'ward_id' => 'nullable|exists:wards,id',
            'role' => 'required|in:admin,staff,superadmin',
        ]);

        // Update basic user information
        $user->update([
            'name' => $request->name,
            'role' => $request->role,
            'ward_id' => $request->ward_id, // Update the main ward_id as well
        ]);

        // Sync ward assignment for all users
        if ($request->ward_id) {
            $user->wards()->sync([$request->ward_id]);
        } else {
            $user->wards()->detach();
        }

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:6|confirmed',
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
