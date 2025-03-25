<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DischargeLog;
use App\Models\Room;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with ward and bed information.
     */
    public function index()
    {
        // Get the selected ward from the session
        $selectedWardId = session('selected_ward_id');

        if (!$selectedWardId) {
            return redirect()->route('select.ward');
        }

        $ward = Ward::with(['rooms.beds' => function ($query) {
            $query->orderBy('bed_number');
        }])->findOrFail($selectedWardId);

        // Count total beds in the ward
        $totalBeds = Bed::whereHas('room', function ($query) use ($selectedWardId) {
            $query->where('ward_id', $selectedWardId);
        })->count();

        // Count beds by status
        $bedCounts = [
            'available' => Bed::whereHas('room', function ($query) use ($selectedWardId) {
                $query->where('ward_id', $selectedWardId);
            })->where('status', 'Available')->count(),

            'booked' => Bed::whereHas('room', function ($query) use ($selectedWardId) {
                $query->where('ward_id', $selectedWardId);
            })->where('status', 'Booked')->count(),

            'occupied' => Bed::whereHas('room', function ($query) use ($selectedWardId) {
                $query->where('ward_id', $selectedWardId);
            })->where('status', 'Occupied')->count(),

            'discharged' => Bed::whereHas('room', function ($query) use ($selectedWardId) {
                $query->where('ward_id', $selectedWardId);
            })->where('status', 'Discharged')->count(),
        ];

        // Calculate percentages
        $percentages = [
            'available' => $totalBeds > 0 ? round(($bedCounts['available'] / $totalBeds) * 100) : 0,
            'booked' => $totalBeds > 0 ? round(($bedCounts['booked'] / $totalBeds) * 100) : 0,
            'occupied' => $totalBeds > 0 ? round(($bedCounts['occupied'] / $totalBeds) * 100) : 0,
            'discharged' => $totalBeds > 0 ? round(($bedCounts['discharged'] / $totalBeds) * 100) : 0,
        ];

        // Get current date and time
        $currentDateTime = Carbon::now()->format('F d, Y - h:i A');

        // Get recent discharges from the discharge_logs table
        $recentDischarges = DischargeLog::whereHas('room', function ($query) use ($selectedWardId) {
            $query->where('ward_id', $selectedWardId);
        })
        ->with(['bed', 'room'])
        ->orderBy('discharged_at', 'desc')
        ->take(5)
        ->get();

        // Get today's discharge count
        $todayDischarges = DischargeLog::whereHas('room', function ($query) use ($selectedWardId) {
            $query->where('ward_id', $selectedWardId);
        })
        ->whereDate('discharged_at', Carbon::today())
        ->count();

        return view('dashboard', compact(
            'ward',
            'bedCounts',
            'percentages',
            'totalBeds',
            'currentDateTime',
            'recentDischarges',
            'todayDischarges'
        ));
    }
}
