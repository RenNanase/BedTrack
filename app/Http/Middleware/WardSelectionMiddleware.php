<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class WardSelectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip ward selection check for admin routes
        if (Auth::user()->is_admin && $request->routeIs('admin.*')) {
            return $next($request);
        }

        // Check if a ward is selected
        if (!$request->session()->has('selected_ward_id')) {
            return redirect()->route('select.ward')
                ->with('message', 'Please select a ward to continue.');
        }

        return $next($request);
    }
}
