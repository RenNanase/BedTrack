<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Room;
use App\Models\Bed;
use App\Models\DischargeLog;
use App\Models\TransferLog;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NurseryWardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('ward.selection');
    }

    public function index()
    {
        try {
            // Get the nursery ward
            $nurseryWard = Ward::where('ward_name', 'Nursery Ward')->first();

            if (!$nurseryWard) {
                Log::error('Nursery ward not found');
                return redirect()->route('dashboard')->with('error', 'Nursery ward not found.');
            }

            // Get the nursery room
            $nurseryRoom = Room::where('ward_id', $nurseryWard->id)
                                ->where('room_name', 'Nursery Room')
                                ->first();

            if (!$nurseryRoom) {
                Log::info('Creating nursery room');
                // Create the nursery room if it doesn't exist
                $nurseryRoom = Room::create([
                    'ward_id' => $nurseryWard->id,
                    'room_name' => 'Nursery Room',
                    'capacity' => 20, // Default capacity
                ]);

                // Create initial cribs
                for ($i = 1; $i <= 20; $i++) {
                    Bed::create([
                        'bed_number' => 'Crib ' . $i,
                        'room_id' => $nurseryRoom->id,
                        'status' => 'Available',
                        'is_crib' => true, // Mark as crib
                    ]);
                }
            }

            // Get all cribs in the nursery room with their relationships
            $cribs = Bed::where('room_id', $nurseryRoom->id)
                        ->where('is_crib', true)
                        ->orderBy('bed_number')
                        ->get();

            // Get ward statistics
            $totalCribs = $cribs->count();
            $availableCribs = $cribs->where('status', 'Available')->count();
            $occupiedCribs = $cribs->where('status', 'Occupied')->count();
            $bookedCribs = $cribs->where('status', 'Booked')->count();
            $housekeepingCribs = $cribs->where('status', 'Housekeeping')->count();

            // Get current date and time
            $currentDateTime = Carbon::now()->format('F d, Y - h:i A');

            // Get recent discharges
            $recentDischarges = DischargeLog::whereHas('room', function ($query) use ($nurseryWard) {
                $query->where('ward_id', $nurseryWard->id);
            })
            ->with(['bed', 'room'])
            ->orderBy('discharged_at', 'desc')
            ->take(3)
            ->get();

            // Get today's discharge count
            $todayDischarges = DischargeLog::whereHas('room', function ($query) use ($nurseryWard) {
                $query->where('ward_id', $nurseryWard->id);
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

            // Get recent transfers
            $transferIns = TransferLog::with([
                'sourceBed.room.ward',
                'destinationBed.room.ward'
            ])
            ->whereHas('destinationBed.room.ward', function($query) use ($nurseryWard) {
                $query->where('id', $nurseryWard->id);
            })
            ->orderBy('transferred_at', 'desc')
            ->take(3)
            ->get();

            $transferOuts = TransferLog::with([
                'sourceBed.room.ward',
                'destinationBed.room.ward'
            ])
            ->whereHas('sourceBed.room.ward', function($query) use ($nurseryWard) {
                $query->where('id', $nurseryWard->id);
            })
            ->orderBy('transferred_at', 'desc')
            ->take(3)
            ->get();

            $recentTransfers = [
                'transfer_ins' => $transferIns,
                'transfer_outs' => $transferOuts
            ];

            // Add debug information
            Log::info('Nursery Ward Data:', [
                'ward_id' => $nurseryWard->id,
                'ward_name' => $nurseryWard->ward_name,
                'room_id' => $nurseryRoom->id,
                'room_name' => $nurseryRoom->room_name,
                'total_cribs' => $totalCribs,
                'available_cribs' => $availableCribs,
                'occupied_cribs' => $occupiedCribs,
                'booked_cribs' => $bookedCribs,
                'cribs' => $cribs->toArray()
            ]);

            return view('nursery.index', compact(
                'cribs',
                'totalCribs',
                'availableCribs',
                'occupiedCribs',
                'bookedCribs',
                'housekeepingCribs',
                'currentDateTime',
                'recentDischarges',
                'todayDischarges',
                'activityLogs',
                'hasMoreLogs',
                'recentTransfers',
                'nurseryWard'
            ));
        } catch (\Exception $e) {
            Log::error('Error in NurseryWardController@index: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'An error occurred while loading the nursery ward.');
        }
    }
}
