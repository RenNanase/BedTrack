<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WardController extends Controller
{
    /**
     * Show the ward selection form.
     */
    public function selectWard()
    {
        $wards = Ward::all();
        return view('auth.select-ward', compact('wards'));
    }

    /**
     * Store the ward selection.
     */
    public function storeWardSelection(Request $request)
    {
        $request->validate([
            'ward_id' => 'required|exists:wards,id',
        ]);

        // Store the selected ward in session
        $request->session()->put('selected_ward_id', $request->ward_id);

        // Create or update the user-ward relationship
        Auth::user()->wards()->syncWithoutDetaching([$request->ward_id]);

        return redirect()->route('dashboard');
    }
}
