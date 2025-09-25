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

            $user = Auth::user();

            // If ward_id is provided, validate access and store it
            if ($request->has('ward_id') && $request->ward_id) {
                // Check if user has access to the selected ward
                if (!in_array($user->role, ['superadmin', 'admin']) &&
                    !$user->wards()->where('ward_id', $request->ward_id)->exists()) {
                    Auth::logout();
                    return back()->withErrors([
                        'ward_id' => 'You do not have access to this ward.',
                    ])->withInput($request->except('password'));
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
            // If user is admin or superadmin, redirect to their dashboard
            elseif (in_array($user->role, ['admin', 'superadmin'])) {
                $route = $user->role === 'superadmin' ? 'super-admin.dashboard' : 'admin.dashboard';
                return redirect()->route($route);
            }
            // If user is emergency role, redirect to emergency dashboard
            elseif ($user->role === 'emergency') {
                return redirect()->route('emergency.dashboard');
            }
            // Otherwise, redirect to ward selection
            else {
                return redirect()->route('select.ward');
            }
        }

        return back()->withErrors([
            'name' => 'The provided credentials do not match our records.',
        ])->onlyInput('name');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
