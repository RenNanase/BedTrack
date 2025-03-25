<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        $wards = Ward::all();
        return view('auth.login', compact('wards'));
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if the user is an admin
            $user = Auth::user();

            // If ward_id is provided, store it in session and update the relationship
            if ($request->has('ward_id') && $request->ward_id) {
                // Store the selected ward in session
                $request->session()->put('selected_ward_id', $request->ward_id);

                // Create or update the user-ward relationship
                $user->wards()->syncWithoutDetaching([$request->ward_id]);

                return redirect()->route('dashboard');
            }
            // If user is admin and no ward is selected, redirect to admin dashboard
            elseif ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            // Otherwise, redirect to ward selection if they haven't selected a ward
            else {
                return redirect()->route('select.ward');
            }
        }

        return back()->withErrors([
            'name' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
