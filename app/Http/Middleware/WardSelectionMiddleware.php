<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WardSelectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip ward selection for superadmin and admin users
        if (Auth::check() && in_array(Auth::user()->role, ['superadmin', 'admin'])) {
            return $next($request);
        }

        // Check if user has selected a ward
        if (!session()->has('selected_ward_id')) {
            return redirect()->route('select.ward');
        }

        // Check if user has access to the selected ward
        $user = Auth::user();
        $selectedWardId = session('selected_ward_id');

        if (!$user->wards()->where('ward_id', $selectedWardId)->exists()) {
            // If user doesn't have access to the selected ward, clear the selection and redirect
            session()->forget('selected_ward_id');
            return redirect()->route('select.ward')
                ->with('error', 'You do not have access to this ward.');
        }

        return $next($request);
    }
}
