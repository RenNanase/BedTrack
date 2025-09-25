<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Room;
use App\Models\Ward;
use App\Models\TransferLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmergencyDashboardController extends Controller
{
    /**
     * Display the emergency department dashboard that shows status of all wards.
     */
    public function index()
    {
        // Get all wards that are not blocked
        $wards = Ward::with([
            'rooms' => function($query) {
                $query->where('is_blocked', false)
                      ->orderBy('sequence')
                      ->with(['beds' => function($query) {
                          $query->select('id', 'bed_number', 'room_id', 'status', 'bed_type', 'has_hazard')
                                ->orderBy('bed_number');
                      }]);
            }
        ])
        ->where('is_blocked', false)
        ->get();

        // Prepare statistics for each ward
        $wardStats = [];
        foreach ($wards as $ward) {
            $totalBeds = 0;
            $availableBeds = 0;
            $occupiedBeds = 0;
            $bookedBeds = 0;
            $dischargedBeds = 0;
            $housekeepingBeds = 0;
            $transferBeds = 0;
            
            foreach ($ward->rooms as $room) {
                $totalBeds += $room->beds->count();
                $availableBeds += $room->beds->where('status', 'Available')->count();
                $occupiedBeds += $room->beds->where('status', 'Occupied')->count();
                $bookedBeds += $room->beds->where('status', 'Booked')->count();
                $dischargedBeds += $room->beds->where('status', 'Discharged')->count();
                $housekeepingBeds += $room->beds->where('status', 'Housekeeping')->count();
                $transferBeds += $room->beds->whereIn('status', ['Transfer-in', 'Transfer-out'])->count();
            }
            
            $wardStats[$ward->id] = [
                'totalBeds' => $totalBeds,
                'availableBeds' => $availableBeds,
                'occupiedBeds' => $occupiedBeds,
                'bookedBeds' => $bookedBeds,
                'dischargedBeds' => $dischargedBeds,
                'housekeepingBeds' => $housekeepingBeds,
                'transferBeds' => $transferBeds,
                'availablePercentage' => $totalBeds > 0 ? round(($availableBeds / $totalBeds) * 100) : 0,
                'occupiedPercentage' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0
            ];
        }

        // Get current date and time
        $currentDateTime = Carbon::now()->format('F d, Y - h:i A');

        // Get room types and bed types for filters
        $roomTypes = Room::distinct()->pluck('room_type')->filter()->toArray();
        $bedTypes = Bed::distinct()->pluck('bed_type')->filter()->toArray();

        return view('emergency.dashboard', compact(
            'wards',
            'wardStats',
            'currentDateTime',
            'roomTypes',
            'bedTypes'
        ));
    }

    /**
     * Get rooms for a specific ward.
     */
    public function getRoomsForWard(Ward $ward)
    {
        try {
            Log::info('Emergency Dashboard: Fetching rooms for ward: ' . $ward->id);
            $rooms = $ward->rooms()
                ->where('is_blocked', false)
                ->select('id', 'room_name')
                ->orderBy('sequence')
                ->get();
            
            Log::info('Found rooms: ' . $rooms->count());
            return response()->json($rooms);
        } catch (\Exception $e) {
            Log::error('Error fetching rooms for ward: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to load rooms'], 500);
        }
    }

    /**
     * Get available beds for a specific room.
     */
    public function getAvailableBedsForRoom(Room $room)
    {
        try {
            Log::info('Emergency Dashboard: Fetching available beds for room: ' . $room->id);
            $beds = $room->beds()
                ->where('status', 'Available')
                ->whereHas('room', function($query) {
                    $query->where('is_blocked', false);
                })
                ->whereHas('room.ward', function($query) {
                    $query->where('is_blocked', false);
                })
                ->select('id', 'bed_number')
                ->orderBy('bed_number')
                ->get();
            
            Log::info('Found available beds: ' . $beds->count());
            return response()->json($beds);
        } catch (\Exception $e) {
            Log::error('Error fetching available beds for room: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to load beds'], 500);
        }
    }

    /**
     * Get ward details for the emergency dashboard modal.
     */
    public function getWardDetails(Ward $ward)
    {
        try {
            Log::info('Fetching ward details for ward: ' . $ward->id);
            
            $rooms = $ward->rooms()
                ->where('is_blocked', false)
                ->orderBy('sequence')
                ->with(['beds' => function($query) {
                    $query->select('id', 'room_id', 'bed_number', 'status', 'patient_name', 'mrn')
                          ->orderBy('bed_number');
                }])
                ->get()
                ->map(function($room) {
                    return [
                        'id' => $room->id,
                        'room_name' => $room->room_name,
                        'beds' => $room->beds->map(function($bed) {
                            return [
                                'id' => $bed->id,
                                'bed_name' => $bed->bed_number,
                                'status' => strtolower($bed->status),
                                'patient' => $bed->status === 'Occupied' ? [
                                    'patient_name' => $bed->patient_name,
                                    'mrn' => $bed->mrn
                                ] : null
                            ];
                        })->values()
                    ];
                });
            
            Log::info('Found rooms for ward details: ' . $rooms->count());
            return response()->json(['rooms' => $rooms]);
        } catch (\Exception $e) {
            Log::error('Error fetching ward details: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to load ward details'], 500);
        }
    }
} 