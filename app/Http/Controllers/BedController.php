<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DischargeLog;
use App\Models\TransferLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Bassinet;
use App\Models\BedStatusLog;

class BedController extends Controller
{
    /**
     * Show the bed details.
     */
    public function show(Bed $bed)
    {
        // Check if this bed is in housekeeping status and cleaning time has elapsed
        if ($bed->status === 'Housekeeping' && $bed->housekeeping_started_at) {
            $isTerminalCleaning = str_contains($bed->housekeeping_remarks ?? '', 'Terminal');
            $completionTime = $isTerminalCleaning 
                ? $bed->housekeeping_started_at->addHours(2) 
                : $bed->housekeeping_started_at->addHour();
            
            // If cleaning time has elapsed, automatically update to Available
            if (now()->greaterThan($completionTime)) {
                // Get the previous status for the log
                $oldStatus = $bed->status;
                
                // Update bed status to Available
                $bed->status = 'Available';
                $bed->status_changed_at = now();
                $bed->housekeeping_started_at = null;
                $bed->housekeeping_remarks = null;
                $bed->save();
                
                // Log the automatic status change
                BedStatusLog::create([
                    'bed_id' => $bed->id,
                    'previous_status' => $oldStatus,
                    'new_status' => 'Available',
                    'housekeeping_remarks' => 'Automatically changed after cleaning completion',
                    'changed_by' => 'System',
                    'changed_at' => now(),
                ]);
                
                ActivityLogger::log(
                    'Automatic Status Change',
                    "Bed {$bed->bed_number} in {$bed->room->room_name} automatically changed from Housekeeping to Available after cleaning completion",
                    Bed::class,
                    $bed->id
                );
            }
        }

        // Get all wards including the current ward for transfers
        $wards = \App\Models\Ward::where('is_blocked', false)
            ->get();

        // Get all available beds for transfer-out (excluding the current bed and beds from blocked rooms)
        $availableBeds = Bed::where('status', 'Available')
            ->where('id', '!=', $bed->id)
            ->whereHas('room', function($query) {
                $query->where('is_blocked', false);
            })
            ->whereHas('room.ward', function($query) {
                $query->where('is_blocked', false);
            })
            ->with('room')
            ->get();

        // Get all beds in transfer-out status for transfer-in (excluding beds from blocked rooms)
        $transferOutBeds = Bed::where('status', 'Transfer-out')
            ->where('id', '!=', $bed->id)
            ->whereHas('room', function($query) {
                $query->where('is_blocked', false);
            })
            ->whereHas('room.ward', function($query) {
                $query->where('is_blocked', false);
            })
            ->with('room')
            ->get();

        return view('beds.show', compact('bed', 'wards', 'availableBeds', 'transferOutBeds'));
    }

    /**
     * Update the bed status.
     */
    public function updateStatus(Request $request, Bed $bed)
    {
        // Debug: Log the request data
        \Log::info('Updating bed status', [
            'bed_id' => $bed->id,
            'current_status' => $bed->status,
            'new_status' => $request->status,
            'request_data' => $request->all(),
            'bed_exists' => $bed->exists,
            'bed_room' => $bed->room_id,
            'request_path' => $request->path(),
            'request_method' => $request->method()
        ]);

        try {
            // Force validation to throw an exception if it fails
            $validated = $request->validate([
                'status' => 'required|in:Available,Occupied,Maintenance,Housekeeping,Discharged,Booked,Transfer-out,Transfer-in',
                'housekeeping_remarks' => 'nullable|string|max:255',
                'patient_name' => 'required_if:status,Booked,Occupied,Transfer-out,Transfer-in|string|max:255',
                'patient_category' => 'required_if:status,Booked,Occupied,Transfer-out,Transfer-in|string|max:255',
                'gender' => 'required_if:status,Booked,Occupied,Transfer-out,Transfer-in|string|max:10',
                'mrn' => 'required_if:status,Booked,Occupied,Transfer-out,Transfer-in|string|max:50',
                'notes' => 'nullable|string',
                'has_hazard' => 'nullable|boolean',
                'hazard_notes' => 'required_if:has_hazard,1|nullable|string',
                'destination_bed_id' => 'required_if:status,Transfer-out|nullable|exists:beds,id,status,Available',
                'destination_ward_id' => 'required_if:status,Transfer-out|nullable|exists:wards,id',
                'source_bed_id' => 'required_if:status,Transfer-in|nullable|exists:beds,id,status,Transfer-out',
                'source_ward_id' => 'required_if:status,Transfer-in|nullable|exists:wards,id',
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            $oldStatus = $bed->status;
            $newStatus = $request->status;

            \Log::info('Processing status change', ['old' => $oldStatus, 'new' => $newStatus]);

            // Special cases handling
            if ($newStatus === 'Transfer-out') {
                // Get the destination bed from validated data
                $destinationBed = Bed::findOrFail($validated['destination_bed_id']);

                // Ensure destination bed is available (validation already checks this)
                if ($destinationBed->status !== 'Available') {
                    return back()->with('error', 'Destination bed is not available');
                }

                // Set the source bed to Transfer-out status first
                // This ensures it gets counted in the Transfer-out count
                $bed->update([
                    'status' => 'Transfer-out',
                    'status_changed_at' => now()
                ]);
                
                // Record this status change
                BedStatusLog::create([
                    'bed_id' => $bed->id,
                    'previous_status' => $oldStatus,
                    'new_status' => 'Transfer-out',
                    'changed_by' => auth()->user()->name,
                    'changed_at' => now(),
                ]);

                // Set the destination bed to Transfer-in status first
                $destinationBed->update([
                    'status' => 'Transfer-in',
                    'patient_name' => $bed->patient_name,
                    'patient_category' => $bed->patient_category,
                    'gender' => $bed->gender,
                    'mrn' => $bed->mrn,
                    'notes' => $bed->notes,
                    'has_hazard' => $bed->has_hazard,
                    'hazard_notes' => $bed->hazard_notes,
                    'status_changed_at' => now(),
                ]);
                
                // Record this status change
                BedStatusLog::create([
                    'bed_id' => $destinationBed->id,
                    'previous_status' => 'Available',
                    'new_status' => 'Transfer-in',
                    'changed_by' => auth()->user()->name,
                    'changed_at' => now(),
                ]);

                // Create transfer log
                TransferLog::create([
                    'source_bed_id' => $bed->id,
                    'source_room_id' => $bed->room_id,
                    'destination_bed_id' => $destinationBed->id,
                    'destination_room_id' => $destinationBed->room_id,
                    'patient_name' => $bed->patient_name,
                    'patient_category' => $bed->patient_category,
                    'gender' => $bed->gender,
                    'mrn' => $bed->mrn,
                    'notes' => $bed->notes,
                    'transferred_at' => now(),
                    'had_hazard' => $bed->has_hazard,
                    'maintained_hazard' => $request->has('has_hazard'),
                    'transfer_remarks' => "Transfer out from {$bed->room->ward->ward_name} - {$bed->room->room_name} - Bed {$bed->bed_number} to {$destinationBed->room->ward->ward_name} - {$destinationBed->room->room_name} - Bed {$destinationBed->bed_number}"
                ]);

                // Log activities
                ActivityLogger::log(
                    'Transfer Out',
                    "Transferred patient {$bed->patient_name} from {$bed->room->room_name} - Bed {$bed->bed_number} to {$destinationBed->room->ward->ward_name} - {$destinationBed->room->room_name} - Bed {$destinationBed->bed_number}",
                    Bed::class,
                    $bed->id
                );

                ActivityLogger::log(
                    'Transfer In',
                    "Received patient {$bed->patient_name} from {$bed->room->ward->ward_name} - {$bed->room->room_name} - Bed {$bed->bed_number}",
                    Bed::class,
                    $destinationBed->id,
                    $destinationBed->room->ward_id
                );

                // Now complete the transfer - update the beds to their final statuses
                // after a short delay to ensure the transfer statuses are recorded
                
                // Update destination bed to Occupied
                $destinationBed->update([
                    'status' => 'Occupied',
                    'occupied_at' => now(),
                    'status_changed_at' => now()
                ]);
                
                // Record this status change
                BedStatusLog::create([
                    'bed_id' => $destinationBed->id,
                    'previous_status' => 'Transfer-in',
                    'new_status' => 'Occupied',
                    'changed_by' => auth()->user()->name,
                    'changed_at' => now(),
                ]);
                
                // Clear source bed
                $bed->update([
                    'status' => 'Available',
                    'patient_name' => null,
                    'patient_category' => null,
                    'gender' => null,
                    'mrn' => null,
                    'notes' => null,
                    'has_hazard' => false,
                    'hazard_notes' => null,
                    'occupied_at' => null,
                    'status_changed_at' => now()
                ]);

                // Record this status change
                BedStatusLog::create([
                    'bed_id' => $bed->id,
                    'previous_status' => 'Transfer-out',
                    'new_status' => 'Available',
                    'changed_by' => auth()->user()->name,
                    'changed_at' => now(),
                ]);

                return redirect()->route('beds.show', $bed)->with('success', 'Patient transferred successfully');
            } else if ($newStatus === 'Discharged') {
                $newStatus = 'Housekeeping';
                $bed->housekeeping_started_at = now();
                
                // Set housekeeping remarks based on hazard status
                if ($bed->has_hazard) {
                    $bed->housekeeping_remarks = 'Terminal Cleaning (2 hours) - Hazardous case';
                } else {
                    $bed->housekeeping_remarks = 'Normal Cleaning (1 hour) - Standard case';
                }

                // Log the discharge
                DischargeLog::create([
                    'bed_id' => $bed->id,
                    'room_id' => $bed->room_id,
                    'patient_name' => $bed->patient_name,
                    'patient_category' => $bed->patient_category,
                    'gender' => $bed->gender,
                    'mrn' => $bed->mrn,
                    'notes' => $bed->notes,
                    'discharged_at' => now(),
                ]);

                // Log the activity
                ActivityLogger::log(
                    'Discharged Patient',
                    "Discharged patient {$bed->patient_name} from {$bed->room->room_name} - Bed {$bed->bed_number}",
                    Bed::class,
                    $bed->id
                );
            } else if ($newStatus === 'Transfer-in') {
                // Get the source bed using the already validated source_bed_id
                $sourceBed = Bed::findOrFail($validated['source_bed_id']);

                // Ensure source bed has a patient (validation already checks status)
                if ($sourceBed->status !== 'Transfer-out') {
                    return back()->with('error', 'Source bed is not in transfer-out status');
                }

                // First, update current bed to Transfer-in status
                $bed->update([
                    'status' => 'Transfer-in',
                    'patient_name' => $sourceBed->patient_name,
                    'patient_category' => $sourceBed->patient_category,
                    'gender' => $sourceBed->gender,
                    'mrn' => $sourceBed->mrn,
                    'notes' => $sourceBed->notes,
                    'has_hazard' => $sourceBed->has_hazard,
                    'hazard_notes' => $sourceBed->hazard_notes,
                    'status_changed_at' => now(),
                ]);
                
                // Record the Transfer-in status change
                BedStatusLog::create([
                    'bed_id' => $bed->id,
                    'previous_status' => $oldStatus,
                    'new_status' => 'Transfer-in',
                    'changed_by' => auth()->user()->name,
                    'changed_at' => now(),
                ]);

                // Log the transfer
                TransferLog::create([
                    'source_bed_id' => $sourceBed->id,
                    'source_room_id' => $sourceBed->room_id,
                    'destination_bed_id' => $bed->id,
                    'destination_room_id' => $bed->room_id,
                    'patient_name' => $sourceBed->patient_name,
                    'patient_category' => $sourceBed->patient_category,
                    'gender' => $sourceBed->gender,
                    'mrn' => $sourceBed->mrn,
                    'notes' => $sourceBed->notes,
                    'transferred_at' => now(),
                    'had_hazard' => $sourceBed->has_hazard,
                    'maintained_hazard' => $request->has('has_hazard'),
                    'transfer_remarks' => "Transfer in from {$sourceBed->room->ward->ward_name} - {$sourceBed->room->room_name} - Bed {$sourceBed->bed_number} to {$bed->room->ward->ward_name} - {$bed->room->room_name} - Bed {$bed->bed_number}"
                ]);

                // Log the activity for destination bed
                ActivityLogger::log(
                    'Transfer In',
                    "Received patient {$sourceBed->patient_name} from {$sourceBed->room->ward->ward_name} - {$sourceBed->room->room_name} - Bed {$sourceBed->bed_number}",
                    Bed::class,
                    $bed->id,
                    $bed->room->ward_id
                );

                // Log the activity for source bed
                ActivityLogger::log(
                    'Transfer Out',
                    "Transferred patient {$sourceBed->patient_name} to {$bed->room->ward->ward_name} - {$bed->room->room_name} - Bed {$bed->bed_number}",
                    Bed::class,
                    $sourceBed->id,
                    $sourceBed->room->ward_id
                );

                // Now complete the transfer - update the beds to their final statuses
                
                // Update current bed (destination) to Occupied
                $bed->update([
                    'status' => 'Occupied',
                    'occupied_at' => now(),
                    'status_changed_at' => now()
                ]);
                
                // Record this status change
                BedStatusLog::create([
                    'bed_id' => $bed->id,
                    'previous_status' => 'Transfer-in',
                    'new_status' => 'Occupied',
                    'changed_by' => auth()->user()->name,
                    'changed_at' => now(),
                ]);

                // Clear source bed
                $sourceBed->update([
                    'status' => 'Available',
                    'patient_name' => null,
                    'patient_category' => null,
                    'gender' => null,
                    'mrn' => null,
                    'notes' => null,
                    'has_hazard' => false,
                    'hazard_notes' => null,
                    'occupied_at' => null,
                    'status_changed_at' => now()
                ]);

                // Record this status change
                BedStatusLog::create([
                    'bed_id' => $sourceBed->id,
                    'previous_status' => 'Transfer-out',
                    'new_status' => 'Available',
                    'changed_by' => auth()->user()->name,
                    'changed_at' => now(),
                ]);

                return redirect()->route('beds.show', $bed)->with('success', 'Patient transferred successfully');
            } else if ($newStatus === 'Housekeeping') {
                $bed->housekeeping_started_at = now();
                
                // If housekeeping remarks are not provided, set them based on hazard status
                if (empty($request->housekeeping_remarks)) {
                    if ($bed->has_hazard) {
                        $bed->housekeeping_remarks = 'Terminal Cleaning (2 hours) - Hazardous case';
                    } else {
                        $bed->housekeeping_remarks = 'Normal Cleaning (1 hour) - Standard case';
                    }
                } else {
                    $bed->housekeeping_remarks = $request->housekeeping_remarks;
                }

                // Log the activity
                ActivityLogger::log(
                    'Housekeeping',
                    "Started housekeeping for {$bed->room->room_name} - Bed {$bed->bed_number}: {$bed->housekeeping_remarks}",
                    Bed::class,
                    $bed->id
                );
            } else {
                // For all other status changes, clear housekeeping info
                $bed->housekeeping_started_at = null;
                $bed->housekeeping_remarks = null;
            }

            // Update patient information if needed
            if (in_array($newStatus, ['Booked', 'Occupied', 'Transfer-out', 'Transfer-in'])) {
                \Log::info('Updating patient information', [
                    'patient_name' => $request->patient_name,
                    'patient_category' => $request->patient_category
                ]);

                $bed->patient_name = $request->patient_name;
                $bed->patient_category = $request->patient_category;
                $bed->gender = $request->gender;
                $bed->mrn = $request->mrn;
                $bed->notes = $request->notes;
                $bed->has_hazard = $request->has('has_hazard');
                $bed->hazard_notes = $request->hazard_notes;
                $bed->occupied_at = now();
                $bed->status_changed_at = now();

                // Log the activity for patient registration
                ActivityLogger::log(
                    'Registered Patient',
                    "Registered patient {$request->patient_name} in {$bed->room->room_name} - Bed {$bed->bed_number}",
                    Bed::class,
                    $bed->id
                );
            } else if ($newStatus === 'Available') {
                // Clear patient information when bed becomes available
                $bed->patient_name = null;
                $bed->patient_category = null;
                $bed->gender = null;
                $bed->mrn = null;
                $bed->notes = null;
                $bed->has_hazard = false;
                $bed->hazard_notes = null;
                $bed->occupied_at = null;
                $bed->status_changed_at = now();

                // Log the activity
                ActivityLogger::log(
                    'Bed Available',
                    "Bed {$bed->bed_number} in {$bed->room->room_name} is now available",
                    Bed::class,
                    $bed->id
                );
            }

            // Always update status_changed_at
            $bed->status_changed_at = now();
            
            // Update the status
            $bed->status = $newStatus;
            
            // Use a transaction for saving changes
            \DB::beginTransaction();
            $saved = $bed->save();
            
            \Log::info('Bed saved', ['success' => $saved, 'bed' => $bed->toArray()]);
            
            if (!$saved) {
                \DB::rollBack();
                throw new \Exception('Failed to save bed status');
            }

            // Log the status change
            BedStatusLog::create([
                'bed_id' => $bed->id,
                'previous_status' => $oldStatus,
                'new_status' => $newStatus,
                'housekeeping_remarks' => $bed->housekeeping_remarks,
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
            
            \DB::commit();

            \Log::info('Bed status update completed successfully');

            return redirect()->route('beds.show', $bed)->with('success', 'Bed status updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error updating bed status', [
                'errors' => $e->errors(),
                'validator' => $e->validator->failed()
            ]);
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating bed status: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Rollback if transaction is in progress
            if (\DB::transactionLevel() > 0) {
                \DB::rollBack();
            }
            
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for updating patient information.
     */
    public function editPatient(Bed $bed)
    {
        return view('beds.edit_patient', compact('bed'));
    }

    /**
     * Update the patient information.
     */
    public function updatePatient(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_category' => 'required|string|max:255',
            'gender' => 'required|string|max:10',
            'mrn' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $bed->update($validated);

        // Log the patient update activity
        ActivityLogger::log(
            'Updated Patient Info',
            "Updated patient information for {$validated['patient_name']} in {$bed->room->room_name} - {$bed->bed_number}",
            Bed::class,
            $bed->id
        );

        return redirect()->route('beds.show', $bed)->with('success', 'Patient information updated successfully.');
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
    
    /**
     * Show the available bassinets for transfer to maternity wards.
     * This method is called via AJAX when a nursery ward crib wants to transfer to maternity.
     */
    public function getAvailableBassinets(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'ward_id' => 'required|exists:wards,id',
            ]);
            
            // Get the ward
            $ward = \App\Models\Ward::findOrFail($validated['ward_id']);
            
            // Check if this is a maternity ward (non-nursery)
            if ($ward->is_nursery) {
                return response()->json(['error' => 'Selected ward is not a maternity ward'], 422);
            }
            
            // Get available bassinets in this ward
            $bassinets = \App\Models\Bassinet::whereHas('room', function($query) use ($ward) {
                    $query->where('ward_id', $ward->id)
                          ->where('is_blocked', false);
                })
                ->where('status', 'Available')
                ->with('room')
                ->get()
                ->map(function($bassinet) {
                    return [
                        'id' => $bassinet->id,
                        'bassinet_number' => $bassinet->bassinet_number,
                        'room_name' => $bassinet->room->room_name,
                        'ward_name' => $bassinet->room->ward->ward_name
                    ];
                });
                
            return response()->json($bassinets);
        } catch (\Exception $e) {
            \Log::error('Error getting available bassinets: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load bassinets: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Transfer from a nursery crib to a maternity bassinet
     */
    public function transferToMaternity(Request $request, Bed $bed)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'destination_ward_id' => 'required|exists:wards,id',
                'transfer_room_id' => 'required|exists:rooms,id',
                'destination_bed_id' => 'required|exists:beds,id',
                'destination_bassinet_id' => 'required|exists:bassinets,id',
            ]);
            
            // Start transaction
            \DB::beginTransaction();
            
            // Ensure this is a crib in nursery ward
            if (!$bed->is_crib) {
                return back()->with('error', 'Only cribs can be transferred to maternity bassinets');
            }
            
            // Make sure bed has a patient
            if ($bed->status !== 'Occupied') {
                return back()->with('error', 'Crib must be occupied to transfer a baby');
            }
            
            // Get the destination bed
            $destinationBed = \App\Models\Bed::findOrFail($validated['destination_bed_id']);
            
            // Ensure destination bed is in a maternity ward
            $isMaternityWard = !$destinationBed->room->ward->is_nursery;
            if (!$isMaternityWard) {
                return back()->with('error', 'Destination must be in a maternity ward');
            }
            
            // Get the destination bassinet
            $bassinet = \App\Models\Bassinet::findOrFail($validated['destination_bassinet_id']);
            
            // Ensure bassinet is available
            if ($bassinet->status !== 'Available') {
                return back()->with('error', 'The selected bassinet is not available');
            }
            
            // First set the source crib to Transfer-out status
            $oldStatus = $bed->status;
            $bed->update([
                'status' => 'Transfer-out',
                'status_changed_at' => now()
            ]);
            
            // Record the status change for tracking
            \App\Models\BedStatusLog::create([
                'bed_id' => $bed->id,
                'previous_status' => $oldStatus,
                'new_status' => 'Transfer-out',
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
            
            // Set the bassinet to Transfer-in status first
            $bassinet->update([
                'status' => 'Transfer-in',
                'patient_name' => $bed->patient_name,
                'gender' => $bed->gender,
                'mrn' => $bed->mrn,
                'patient_category' => 'Newborn',
                'mother_name' => $request->mother_name ?? 'Unknown',
                'mother_mrn' => $request->mother_mrn ?? 'Unknown',
                'notes' => $bed->notes,
                'status_changed_at' => now(),
            ]);
            
            // Now complete the transfer - update to final status
            
            // Update the bassinet with baby information to final status
            $bassinet->update([
                'status' => 'Occupied',
                'occupied_at' => now(),
                'status_changed_at' => now(),
            ]);
            
            // Create transfer log
            \App\Models\TransferLog::create([
                'source_bed_id' => $bed->id,
                'source_room_id' => $bed->room_id,
                'destination_bassinet_id' => $bassinet->id,
                'destination_room_id' => $bassinet->room_id,
                'patient_name' => $bed->patient_name,
                'patient_category' => 'Newborn',
                'gender' => $bed->gender,
                'mrn' => $bed->mrn,
                'transferred_at' => now(),
                'had_hazard' => $bed->has_hazard,
                'maintained_hazard' => false,
                'transfer_remarks' => "Transferred from Nursery Ward Crib {$bed->bed_number} to Maternity Ward Bed {$destinationBed->bed_number} with Bassinet {$bassinet->bassinet_number}"
            ]);
            
            // Clear the crib
            $bed->update([
                'status' => 'Available',
                'patient_name' => null,
                'gender' => null,
                'mrn' => null,
                'patient_category' => null,
                'notes' => null,
                'has_hazard' => false,
                'hazard_notes' => null,
                'occupied_at' => null,
                'status_changed_at' => now()
            ]);
            
            // Record the status change
            \App\Models\BedStatusLog::create([
                'bed_id' => $bed->id,
                'previous_status' => 'Transfer-out',
                'new_status' => 'Available',
                'changed_by' => auth()->user()->name,
                'changed_at' => now(),
            ]);
            
            // Log the activity
            \App\Services\ActivityLogger::log(
                'Transfer to Maternity',
                "Transferred baby {$bassinet->patient_name} from Nursery Ward Crib {$bed->bed_number} to Maternity Ward Bed {$destinationBed->bed_number} with Bassinet {$bassinet->bassinet_number}",
                \App\Models\Bed::class,
                $bed->id
            );
            
            \DB::commit();
            
            return redirect()->route('beds.show', $bed)->with('success', 'Baby transferred successfully to maternity ward');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error transferring to maternity: ' . $e->getMessage());
            return back()->with('error', 'Failed to transfer baby: ' . $e->getMessage());
        }
    }
}
