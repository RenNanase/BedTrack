<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DischargeLog;
use App\Models\Room;
use App\Models\Ward;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ActivityLog;
use App\Models\TransferLog;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with ward and bed information.
     */
    public function index()
    {
        $selectedWardId = session('selected_ward_id');
        if (!$selectedWardId) {
            return redirect()->route('ward.select');
        }

        $ward = Ward::find($selectedWardId);
        $rooms = $ward->rooms()->with(['beds' => function($query) {
            $query->orderBy('bed_number');
        }])->orderBy('sequence')->get();

        // If it's the nursery ward, redirect to nursery.index
        if ($ward->ward_name === 'Nursery Ward') {
            return redirect()->route('nursery.index');
        }

        // Count total beds in the ward
        $totalBeds = Bed::whereHas('room', function ($query) use ($ward) {
            $query->where('ward_id', $ward->id);
        })->count();

        // Count beds by status
        $bedCounts = [
            'available' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->where('status', 'Available')->count(),

            'booked' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->where('status', 'Booked')->count(),

            'occupied' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->where('status', 'Occupied')->count(),

            'discharged' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->where('status', 'Discharged')->count(),

            'housekeeping' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->where('status', 'Housekeeping')->count(),
            
            'transfer_in' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->where('status', 'Transfer-in')->count(),
        ];

        // Calculate percentages
        $percentages = [
            'available' => $totalBeds > 0 ? round(($bedCounts['available'] / $totalBeds) * 100) : 0,
            'booked' => $totalBeds > 0 ? round(($bedCounts['booked'] / $totalBeds) * 100) : 0,
            'occupied' => $totalBeds > 0 ? round(($bedCounts['occupied'] / $totalBeds) * 100) : 0,
            'discharged' => $totalBeds > 0 ? round(($bedCounts['discharged'] / $totalBeds) * 100) : 0,
            'housekeeping' => $totalBeds > 0 ? round(($bedCounts['housekeeping'] / $totalBeds) * 100) : 0,
        ];

        // Get current date and time
        $currentDateTime = Carbon::now()->format('F d, Y - h:i A');

        // Get recent discharges from the discharge_logs table
        $recentDischarges = DischargeLog::whereHas('room', function ($query) use ($ward) {
            $query->where('ward_id', $ward->id);
        })
        ->with(['bed', 'room'])
        ->orderBy('discharged_at', 'desc')
        ->take(3)
        ->get();

        // Get today's discharge count
        $todayDischarges = DischargeLog::whereHas('room', function ($query) use ($ward) {
            $query->where('ward_id', $ward->id);
        })
        ->whereDate('discharged_at', Carbon::today())
        ->count();

        // Get recent activity logs
        $activityLogs = ActivityLogger::getRecent(3);

        // Check if there are more logs available
        $hasMoreLogs = ActivityLog::where('action', '!=', 'Viewed Dashboard')
            ->orderBy('created_at', 'desc')
            ->skip(3)
            ->take(1)
            ->exists();

        $recentTransfers = $this->getRecentTransfers();

        return view('dashboard', compact(
            'ward',
            'rooms',
            'bedCounts',
            'percentages',
            'totalBeds',
            'currentDateTime',
            'recentDischarges',
            'todayDischarges',
            'activityLogs',
            'hasMoreLogs',
            'recentTransfers'
        ));
    }

    private function getRecentDischarges()
    {
        return Bed::where('status', 'Discharged')
            ->whereNotNull('discharged_at')
            ->orderBy('discharged_at', 'desc')
            ->take(5)
            ->get();
    }

    private function getRecentTransfers()
    {
        $selectedWardId = session('selected_ward_id');

        if (!$selectedWardId) {
            return [
                'transfer_ins' => collect(),
                'transfer_outs' => collect()
            ];
        }

        $transferIns = TransferLog::with([
            'sourceBed.room.ward',
            'destinationBed.room.ward'
        ])
        ->whereHas('destinationBed.room.ward', function($query) use ($selectedWardId) {
            $query->where('id', $selectedWardId);
        })
        ->orderBy('transferred_at', 'desc')
        ->take(3)
        ->get();

        $transferOuts = TransferLog::with([
            'sourceBed.room.ward',
            'destinationBed.room.ward'
        ])
        ->whereHas('sourceBed.room.ward', function($query) use ($selectedWardId) {
            $query->where('id', $selectedWardId);
        })
        ->orderBy('transferred_at', 'desc')
        ->take(3)
        ->get();

        return [
            'transfer_ins' => $transferIns,
            'transfer_outs' => $transferOuts
        ];
    }

    private function getWardStats($ward)
    {
        $totalBeds = $ward->rooms->sum('capacity');
        $bedCounts = [
            'available' => $ward->rooms->flatMap->beds->where('status', 'Available')->count(),
            'booked' => $ward->rooms->flatMap->beds->where('status', 'Booked')->count(),
            'occupied' => $ward->rooms->flatMap->beds->where('status', 'Occupied')->count(),
            'housekeeping' => $ward->rooms->flatMap->beds->where('status', 'Housekeeping')->count(),
            'transfer_in' => $ward->rooms->flatMap->beds->where('status', 'Transfer-in')->count(),
        ];
        $todayDischarges = $ward->rooms->flatMap->beds
            ->where('status', 'Discharged')
            ->whereNotNull('discharged_at')
            ->whereDate('discharged_at', Carbon::today())
            ->count();

        return [
            'totalBeds' => $totalBeds,
            'bedCounts' => $bedCounts,
            'todayDischarges' => $todayDischarges
        ];
    }
}
