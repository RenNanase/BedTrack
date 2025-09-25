<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DischargeLog;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Check if the current user is an admin
     */
    private function checkAdmin()
    {
        if (!Auth::check()) {
            abort(403, 'User not authenticated.');
        }

        $user = Auth::user();
        Log::info('Checking admin access for user:', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ]);

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action. Admin access required.');
        }
    }

    /**
     * Show the admin dashboard with all wards summary.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $this->checkAdmin();

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

            // Get active beds (not in blocked rooms)
            $activeBedsQuery = Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id)
                      ->where('is_blocked', false);
            });
            $activeBeds = $activeBedsQuery->get();

            // Count total beds (excluding those in blocked rooms)
            $totalBeds = $activeBeds->count();

            // Count beds by status (only count active beds)
            $availableBeds = $activeBeds->where('status', 'Available')->count();
            $bookedBeds = $activeBeds->where('status', 'Booked')->count();
            $occupiedBeds = $activeBeds->where('status', 'Occupied')->count();
            $dischargedBeds = $activeBeds->where('status', 'Discharged')->count();
            
            // For transfers, count ALL beds regardless of room blocked status
            $transferOutBeds = $beds->where('status', 'Transfer-out')->count();
            $transferInBeds = $beds->where('status', 'Transfer-in')->count();
            
            // Count blocked rooms
            $blockedRooms = $ward->rooms()->where('is_blocked', true)->count();
            
            // Count beds in blocked rooms
            $bedsInBlockedRooms = Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id)
                      ->where('is_blocked', true);
            })->count();

            // Also count today's transfers from transfer logs
            $today = \Carbon\Carbon::today();
            $transferOutLogsCount = \App\Models\TransferLog::whereHas('sourceRoom', function($query) use ($ward) {
                    $query->where('ward_id', $ward->id);
                })
                ->whereDate('transferred_at', $today)
                ->count();
                
            $transferInLogsCount = \App\Models\TransferLog::whereHas('destinationRoom', function($query) use ($ward) {
                    $query->where('ward_id', $ward->id);
                })
                ->whereDate('transferred_at', $today)
                ->count();

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
                'transfer_out_beds' => $transferOutBeds,
                'transfer_in_beds' => $transferInBeds,
                'blocked_rooms' => $blockedRooms,
                'beds_in_blocked_rooms' => $bedsInBlockedRooms,
                'today_discharges' => $todayDischarges,
                'today_transfer_out' => $transferOutLogsCount,
                'today_transfer_in' => $transferInLogsCount,
                'available_percentage' => $totalBeds > 0 ? round(($availableBeds / $totalBeds) * 100) : 0,
                'occupied_percentage' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0,
            ];
        }

        // Get current date and time
        $currentDateTime = Carbon::now()->format('F d, Y - h:i A');

        return view('admin.dashboard', compact('wardSummaries', 'currentDateTime'));
    }
}
