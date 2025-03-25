<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DischargeLog;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with all wards summary.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        // Get all wards with their rooms and beds
        $wards = Ward::with(['rooms.beds' => function ($query) {
            $query->orderBy('bed_number');
        }])->get();

        $wardSummaries = [];

        foreach ($wards as $ward) {
            // Get all beds in this ward
            $beds = Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->get();

            // Count total beds
            $totalBeds = $beds->count();

            // Count beds by status
            $availableBeds = $beds->where('status', 'Available')->count();
            $bookedBeds = $beds->where('status', 'Booked')->count();
            $occupiedBeds = $beds->where('status', 'Occupied')->count();
            $dischargedBeds = $beds->where('status', 'Discharged')->count();

            // Get today's discharge count
            $todayDischarges = DischargeLog::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })
            ->whereDate('discharged_at', Carbon::today())
            ->count();

            // Add to ward summaries array
            $wardSummaries[] = [
                'ward' => $ward,
                'total_beds' => $totalBeds,
                'available_beds' => $availableBeds,
                'booked_beds' => $bookedBeds,
                'occupied_beds' => $occupiedBeds,
                'discharged_beds' => $dischargedBeds,
                'today_discharges' => $todayDischarges,
                'available_percentage' => $totalBeds > 0 ? round(($availableBeds / $totalBeds) * 100) : 0,
                'occupied_percentage' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0,
            ];
        }

        // Get current date and time
        $currentDateTime = Carbon::now()->format('F d, Y - h:i A');

        return view('admin.dashboard', compact('wardSummaries', 'currentDateTime'));
    }
}
