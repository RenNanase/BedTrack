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
        // Get all wards except the current bed's ward
        $wards = \App\Models\Ward::where('id', '!=', $bed->room->ward_id)
            ->where('is_blocked', false)
            ->get();

        // Get all available beds for transfer-out (excluding beds from blocked rooms)
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
        $request->validate([
            'status' => 'required|in:Available,Occupied,Maintenance,Housekeeping,Discharged,Booked,Transfer-out,Transfer-in',
            'housekeeping_remarks' => 'nullable|string|max:255',
            'patient_name' => 'required_if:status,Booked,Occupied,Transfer-out,Transfer-in|string|max:255',
            'patient_category' => 'required_if:status,Booked,Occupied,Transfer-out,Transfer-in|string|max:255',
            'gender' => 'required_if:status,Booked,Occupied,Transfer-out,Transfer-in|string|max:10',
            'mrn' => 'required_if:status,Booked,Occupied,Transfer-out,Transfer-in|string|max:50',
            'notes' => 'nullable|string',
            'has_hazard' => 'nullable|boolean',
            'hazard_notes' => 'nullable|string',
        ]);

        $oldStatus = $bed->status;
        $newStatus = $request->status;

        // If status is Discharged, automatically set to Housekeeping
        if ($newStatus === 'Discharged') {
            $newStatus = 'Housekeeping';
            $bed->housekeeping_started_at = now();
            
            // Set housekeeping remarks based on hazard status
            if ($bed->has_hazard) {
                $bed->housekeeping_remarks = 'Terminal Cleaning (2 hours) - Hazardous case';
            } else {
                $bed->housekeeping_remarks = 'Normal Cleaning (1 hour) - Standard case';
            }
        } else if ($newStatus === 'Housekeeping') {
            $bed->housekeeping_started_at = now();
            // If housekeeping remarks are not provided, set them based on hazard status
            if (!$request->housekeeping_remarks) {
                if ($bed->has_hazard) {
                    $bed->housekeeping_remarks = 'Terminal Cleaning (2 hours) - Hazardous case';
                } else {
                    $bed->housekeeping_remarks = 'Normal Cleaning (1 hour) - Standard case';
                }
            } else {
                $bed->housekeeping_remarks = $request->housekeeping_remarks;
            }
        } else {
            $bed->housekeeping_started_at = null;
            $bed->housekeeping_remarks = null;
        }

        // Update patient information if needed
        if (in_array($newStatus, ['Booked', 'Occupied', 'Transfer-out', 'Transfer-in'])) {
            $bed->patient_name = $request->patient_name;
            $bed->patient_category = $request->patient_category;
            $bed->gender = $request->gender;
            $bed->mrn = $request->mrn;
            $bed->notes = $request->notes;
            $bed->has_hazard = $request->has_hazard ?? false;
            $bed->hazard_notes = $request->hazard_notes;
            $bed->occupied_at = now();
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
        }

        $bed->status = $newStatus;
        $bed->save();

        // Log the status change
        BedStatusLog::create([
            'bed_id' => $bed->id,
            'previous_status' => $oldStatus,
            'new_status' => $newStatus,
            'housekeeping_remarks' => $bed->housekeeping_remarks,
            'changed_by' => auth()->user()->name,
            'changed_at' => now(),
        ]);

        // Log the activity for patient registration
        if (in_array($newStatus, ['Booked', 'Occupied', 'Transfer-out', 'Transfer-in'])) {
            ActivityLogger::log(
                'Registered Patient',
                "Registered patient {$request->patient_name} in {$bed->room->room_name} - Bed {$bed->bed_number}",
                Bed::class,
                $bed->id
            );
        }

        return redirect()->route('beds.show', $bed)->with('success', 'Bed status updated successfully');
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
}
