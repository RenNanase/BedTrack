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
     * 
     * The bed count logic works as follows:
     * 1. Beds in blocked rooms are NEVER counted as available
     * 2. Total beds shown are only those in active (non-blocked) rooms
     * 3. Status counts (available, booked, occupied, etc.) only include beds in active rooms
     * 4. The 'blocked' count shows the number of rooms that are blocked
     * 5. Transfer-in and Transfer-out counts MUST include all beds with those statuses, as they
     *    represent important transitional states that need to be tracked for reporting purposes
     */
    public function index()
    {
        // Removed problematic Model::flushEventListeners() and Model::clearBootedModels() calls
        
        $selectedWardId = session('selected_ward_id');
        if (!$selectedWardId) {
            return redirect()->route('select.ward');
        }

        $ward = Ward::find($selectedWardId);
        $rooms = $ward->rooms()->with(['beds' => function($query) {
            $query->select('id', 'bed_number', 'room_id', 'status', 'patient_name', 'patient_category', 'gender', 'mrn', 'has_hazard', 'hazard_notes', 'occupied_at', 'notes')
                  ->orderBy('bed_number');
        }, 'bassinets' => function($query) {
            $query->select('id', 'bassinet_number', 'room_id', 'status', 'patient_name', 'gender', 'mrn', 'mother_name', 'mother_mrn', 'occupied_at', 'notes')
                  ->orderBy('bassinet_number');
        }])->orderBy('sequence')->get();

        // If it's the nursery ward, redirect to nursery.index
        if ($ward->ward_name === 'Nursery Ward') {
            return redirect()->route('nursery.index');
        }

        // Count total beds in the ward (excluding beds in blocked rooms)
        $totalBeds = Bed::whereHas('room', function ($query) use ($ward) {
            $query->where('ward_id', $ward->id)
                  ->where('is_blocked', false);
        })->count();

        // Count beds by status
        $bedCounts = [
            'available' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id)
                      ->where('is_blocked', false);
            })->where('status', 'Available')->count(),

            'booked' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id)
                      ->where('is_blocked', false);
            })->where('status', 'Booked')->count(),

            'occupied' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id)
                      ->where('is_blocked', false);
            })->where('status', 'Occupied')->count(),

            'discharged' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id)
                      ->where('is_blocked', false);
            })->where('status', 'Discharged')->count(),

            'housekeeping' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id)
                      ->where('is_blocked', false);
            })->where('status', 'Housekeeping')->count(),
            
            // For transfer counts, we want to count ALL transfers regardless of room status,
            // since transfers are important records even for blocked rooms
            'transfer_in' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->where('status', 'Transfer-in')->count(),
            
            'transfer_out' => Bed::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->where('status', 'Transfer-out')->count(),
        ];
        
        // Count blocked rooms and beds in blocked rooms
        $blockedRooms = \App\Models\Room::where('ward_id', $ward->id)
            ->where('is_blocked', true)
            ->count();
            
        $bedsInBlockedRooms = Bed::whereHas('room', function ($query) use ($ward) {
            $query->where('ward_id', $ward->id)
                  ->where('is_blocked', true);
        })->count();
        
        $bedCounts['blocked'] = $blockedRooms;

        // Also count transfer logs to ensure we catch all transfers 
        // (especially important for quick transfers that might skip status updates)
        $today = \Carbon\Carbon::today();
        $transferOutCount = \App\Models\TransferLog::whereHas('sourceRoom', function($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })
            ->whereDate('transferred_at', $today)
            ->count();
            
        $transferInCount = \App\Models\TransferLog::whereHas('destinationRoom', function($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })
            ->whereDate('transferred_at', $today)
            ->count();
            
        // Add the transfer log counts to the dashboard data
        $bedCounts['today_transfer_out'] = $transferOutCount;
        $bedCounts['today_transfer_in'] = $transferInCount;

        // Calculate percentages
        $percentages = [
            'available' => $totalBeds > 0 ? round(($bedCounts['available'] / $totalBeds) * 100) : 0,
            'booked' => $totalBeds > 0 ? round(($bedCounts['booked'] / $totalBeds) * 100) : 0,
            'occupied' => $totalBeds > 0 ? round(($bedCounts['occupied'] / $totalBeds) * 100) : 0,
            'discharged' => $totalBeds > 0 ? round(($bedCounts['discharged'] / $totalBeds) * 100) : 0,
            'housekeeping' => $totalBeds > 0 ? round(($bedCounts['housekeeping'] / $totalBeds) * 100) : 0,
        ];

        // Count bassinets by status (only for Maternity ward)
        $bassinetCounts = [];
        $totalBassinets = 0;
        
        // Check if we're in a maternity ward (any non-nursery ward)
        if (!$ward->is_nursery) {
            $totalBassinets = \App\Models\Bassinet::whereHas('room', function ($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->count();
            
            $bassinetCounts = [
                'available' => \App\Models\Bassinet::whereHas('room', function ($query) use ($ward) {
                    $query->where('ward_id', $ward->id);
                })->where('status', 'Available')->count(),

                'occupied' => \App\Models\Bassinet::whereHas('room', function ($query) use ($ward) {
                    $query->where('ward_id', $ward->id);
                })->where('status', 'Occupied')->count(),

                'transfer_out' => \App\Models\Bassinet::whereHas('room', function ($query) use ($ward) {
                    $query->where('ward_id', $ward->id);
                })->where('status', 'Transfer-out')->count(),
                
                'transfer_in' => \App\Models\Bassinet::whereHas('room', function ($query) use ($ward) {
                    $query->where('ward_id', $ward->id);
                })->where('status', 'Transfer-in')->count(),
                
                'discharged' => \Illuminate\Support\Facades\DB::table('activity_log')
                    ->where('log_name', 'default')
                    ->where('description', 'LIKE', '%discharged baby from bassinet%')
                    ->whereDate('created_at', \Carbon\Carbon::today())
                    ->whereJsonContains('properties->attributes->ward_id', $ward->id)
                    ->orWhereJsonContains('properties->ward_id', $ward->id)
                    ->count(),
            ];
        }

        // Get current date and time
        $currentDateTime = Carbon::now()->format('F d, Y - h:i A');

        // Get recent activity logs
        $bedActivityLogs = ActivityLog::with('user')
            ->where('action', '!=', 'Viewed Dashboard')
            ->when($ward->id, function ($query) use ($ward) {
                return $query->where('ward_id', $ward->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get bassinet activities directly from the activity_log table
        $bassinetActivities = \Spatie\Activitylog\Models\Activity::query()
            ->where(function($query) {
                $query->where('log_name', 'default')
                      ->orWhere('description', 'LIKE', '%bassinet%')
                      ->orWhere('description', 'LIKE', '%baby%');
            })
            ->where(function($query) use ($ward) {
                $query->whereJsonContains('properties->ward_id', $ward->id)
                    ->orWhereJsonContains('properties->attributes->ward_id', $ward->id)
                    ->orWhereJsonContains('properties->attributes->room.ward_id', $ward->id)
                    ->orWhereJsonContains('properties->ward.id', $ward->id)
                    ->orWhereJsonContains('properties->room.ward_id', $ward->id)
                    ->orWhere(function($q) use ($ward) {
                        $q->where('properties->ward_id', $ward->id);
                    })
                    ->orWhere(function($q) use ($ward) {
                        $q->where('description', 'LIKE', '%bassinet%')
                          ->where('description', 'LIKE', '%ward_id:'.$ward->id.'%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Combine the activity logs
        $combinedActivities = $bedActivityLogs->merge($bassinetActivities)
            ->sortByDesc('created_at')
            ->take(5);

        // Make sure all activities have the required properties for display
        $processedActivities = $combinedActivities->map(function ($activity) {
            // If it's a spatie activity log (from bassinets)
            if (isset($activity->log_name) && $activity->log_name == 'default') {
                // Make sure description is properly formatted
                if (isset($activity->description)) {
                    $activity->description = ucfirst($activity->description);
                }
                
                // If the activity has a subject (usually a Bassinet model)
                if (isset($activity->subject_type) && str_contains($activity->subject_type, 'Bassinet') && isset($activity->subject_id)) {
                    try {
                        // Try to load the bassinet to get additional info
                        $bassinet = \App\Models\Bassinet::with('room')->find($activity->subject_id);
                        if ($bassinet) {
                            // Add bassinet data to the properties
                            $properties = $activity->properties ?? new \stdClass();
                            if (is_object($properties)) {
                                if (!isset($properties->attributes)) {
                                    $properties->attributes = new \stdClass();
                                }
                                
                                // Add bassinet details
                                $properties->attributes->bassinet_number = $bassinet->bassinet_number;
                                
                                // Add room details if available
                                if ($bassinet->room) {
                                    if (!isset($properties->attributes->room)) {
                                        $properties->attributes->room = new \stdClass();
                                    }
                                    $properties->attributes->room->room_name = $bassinet->room->room_name;
                                    $properties->attributes->room->id = $bassinet->room->id;
                                    $properties->attributes->room->ward_id = $bassinet->room->ward_id;
                                }
                                
                                $activity->properties = $properties;
                            }
                        }
                    } catch (\Exception $e) {
                        // Silently handle exceptions - old bassinets might not exist
                    }
                }
            }
            return $activity;
        });

        $activityLogs = $processedActivities;

        // Check if there are more logs available
        $hasMoreLogs = app(ActivityLogger::class)->hasMoreLogs(5, 1, $ward->id);

        // Get recent discharges including both bed and bassinet discharges
        $recentDischarges = DischargeLog::whereHas('room', function ($query) use ($ward) {
            $query->where('ward_id', $ward->id);
        })
        ->with(['bed', 'room'])
        ->orderBy('discharged_at', 'desc')
        ->take(3)
        ->get();

        // Add bassinet discharges from activity log
        $bassinetDischarges = \Spatie\Activitylog\Models\Activity::query()
            ->where('log_name', 'default')
            ->where('description', 'LIKE', '%discharged baby from bassinet%')
            ->where(function($query) use ($ward) {
                $query->whereJsonContains('properties->ward_id', $ward->id)
                    ->orWhereJsonContains('properties->attributes->ward_id', $ward->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($activity) {
                $props = $activity->properties;
                $properties = $props->toArray();
                
                // Create a discharge-like object
                $discharge = new \stdClass();
                $discharge->discharged_at = $activity->created_at;
                $discharge->is_bassinet = true;
                
                // Extract patient info from properties
                if (isset($properties['baby_name'])) {
                    $discharge->patient_name = $properties['baby_name'];
                } else {
                    $discharge->patient_name = 'Baby';
                }
                
                if (isset($properties['from_bassinet'])) {
                    $discharge->bassinet_number = $properties['from_bassinet'];
                }
                
                $discharge->bassinet = new \stdClass();
                $discharge->bassinet->bassinet_number = $discharge->bassinet_number ?? 'Unknown';
                
                if (isset($properties['attributes']['room_id'])) {
                    $discharge->room_id = $properties['attributes']['room_id'];
                    $discharge->room = \App\Models\Room::find($discharge->room_id);
                } else {
                    $discharge->room = new \stdClass();
                    $discharge->room->room_name = 'Maternity Ward';
                }
                
                return $discharge;
            });

        // Combine bed and bassinet discharges
        $combinedDischarges = $recentDischarges->concat($bassinetDischarges)
            ->sortByDesc(function($discharge) {
                return $discharge->discharged_at;
            })
            ->take(3);

        $recentDischarges = $combinedDischarges;

        // Get today's discharge count
        $todayDischarges = DischargeLog::whereHas('room', function ($query) use ($ward) {
            $query->where('ward_id', $ward->id);
        })
        ->whereDate('discharged_at', Carbon::today())
        ->count();

        $recentTransfers = $this->getRecentTransfers();

        // Get all wards for transfer modal
        $wards = Ward::all();

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
            'recentTransfers',
            'wards',
            'bassinetCounts',
            'totalBassinets'
        ));
    }

    private function getRecentDischarges()
    {
        return Bed::where('status', 'Discharged')
            ->whereNotNull('discharged_at')
            ->orderBy('discharged_at', 'desc')
            ->take(3)
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
            'destinationBed.room.ward',
            'sourceBassinet.room.ward',
            'destinationBassinet.room.ward'
        ])
        ->where(function($query) use ($selectedWardId) {
            $query->whereHas('destinationBed.room.ward', function($q) use ($selectedWardId) {
                $q->where('id', $selectedWardId);
            })
            ->orWhereHas('destinationBassinet.room.ward', function($q) use ($selectedWardId) {
                $q->where('id', $selectedWardId);
            });
        })
        ->orderBy('transferred_at', 'desc')
        ->take(3)
        ->get();

        $transferOuts = TransferLog::with([
            'sourceBed.room.ward',
            'destinationBed.room.ward',
            'sourceBassinet.room.ward',
            'destinationBassinet.room.ward'
        ])
        ->where(function($query) use ($selectedWardId) {
            $query->whereHas('sourceBed.room.ward', function($q) use ($selectedWardId) {
                $q->where('id', $selectedWardId);
            })
            ->orWhereHas('sourceBassinet.room.ward', function($q) use ($selectedWardId) {
                $q->where('id', $selectedWardId);
            });
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
        // Get all rooms in the ward that are not blocked
        $activeRooms = $ward->rooms->where('is_blocked', false);
        
        // Count total beds in active (non-blocked) rooms
        $totalBeds = $activeRooms->sum('capacity');
        
        // Count beds in active rooms by status
        $bedCounts = [
            'available' => $activeRooms->flatMap->beds->where('status', 'Available')->count(),
            'booked' => $activeRooms->flatMap->beds->where('status', 'Booked')->count(),
            'occupied' => $activeRooms->flatMap->beds->where('status', 'Occupied')->count(),
            'housekeeping' => $activeRooms->flatMap->beds->where('status', 'Housekeeping')->count(),
            
            // For transfers, count ALL rooms regardless of blocked status
            'transfer_in' => $ward->rooms->flatMap->beds->where('status', 'Transfer-in')->count(),
            'transfer_out' => $ward->rooms->flatMap->beds->where('status', 'Transfer-out')->count(),
        ];
        
        // Count blocked rooms
        $blockedRooms = $ward->rooms->where('is_blocked', true)->count();
        $bedCounts['blocked'] = $blockedRooms;
        
        $todayDischarges = $activeRooms->flatMap->beds
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
