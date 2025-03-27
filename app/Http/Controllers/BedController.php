<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DischargeLog;
use App\Models\TransferLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BedController extends Controller
{
    /**
     * Show the bed details.
     */
    public function show(Bed $bed)
    {
        // Get all wards except the current bed's ward
        $wards = \App\Models\Ward::where('id', '!=', $bed->room->ward_id)->get();

        // Get all available beds for transfer-out
        $availableBeds = Bed::where('status', 'Available')
            ->where('id', '!=', $bed->id)
            ->with('room')
            ->get();

        // Get all beds in transfer-out status for transfer-in
        $transferOutBeds = Bed::where('status', 'Transfer-out')
            ->where('id', '!=', $bed->id)
            ->with('room')
            ->get();

        return view('beds.show', compact('bed', 'wards', 'availableBeds', 'transferOutBeds'));
    }

    /**
     * Update the bed status.
     */
    public function updateStatus(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Available', 'Booked', 'Occupied', 'Discharged', 'Housekeeping', 'Transfer-in', 'Transfer-out'])],
            'patient_name' => 'nullable|required_unless:status,Available,Housekeeping|string|max:255',
            'patient_category' => 'nullable|required_unless:status,Available,Housekeeping|string|max:255',
            'gender' => 'nullable|required_unless:status,Available,Housekeeping|string|max:10',
            'mrn' => 'nullable|required_unless:status,Available,Housekeeping|string|max:50',
            'notes' => 'nullable|string',
            'has_hazard' => 'boolean',
            'hazard_notes' => 'nullable|string|max:500',
            'transfer_destination_bed_id' => 'nullable|required_if:status,Transfer-out|exists:beds,id',
            'transfer_source_bed_id' => 'nullable|required_if:status,Transfer-in|exists:beds,id',
        ]);

        $oldStatus = $bed->status;
        $newStatus = $validated['status'];

        // Handle Transfer-out process
        if ($newStatus === 'Transfer-out') {
            // Validate destination bed
            $destinationBed = Bed::findOrFail($validated['transfer_destination_bed_id']);

            if ($destinationBed->status !== 'Available') {
                return back()->with('error', 'Destination bed is not available for transfer.');
            }

            // Create transfer log entry
            TransferLog::create([
                'source_bed_id' => $bed->id,
                'destination_bed_id' => $destinationBed->id,
                'source_room_id' => $bed->room_id,
                'destination_room_id' => $destinationBed->room_id,
                'patient_name' => $bed->patient_name,
                'patient_category' => $bed->patient_category,
                'gender' => $bed->gender,
                'mrn' => $bed->mrn,
                'notes' => $bed->notes,
                'transferred_at' => Carbon::now(),
            ]);

            // Log the transfer activity
            ActivityLogger::log(
                'Transfer Patient',
                "Transferred patient {$bed->patient_name} from {$bed->room->room_name} - {$bed->bed_number} to {$destinationBed->room->room_name} - {$destinationBed->bed_number}",
                Bed::class,
                $bed->id
            );

            // Update destination bed
            $destinationBed->update([
                'status' => 'Transfer-in',
                'patient_name' => $bed->patient_name,
                'patient_category' => $bed->patient_category,
                'gender' => $bed->gender,
                'mrn' => $bed->mrn,
                'notes' => $bed->notes,
                'status_changed_at' => Carbon::now(),
            ]);

            // Clear source bed
            $bed->update([
                'status' => 'Available',
                'patient_name' => null,
                'patient_category' => null,
                'gender' => null,
                'mrn' => null,
                'notes' => null,
                'status_changed_at' => Carbon::now(),
            ]);

            return redirect()->route('dashboard')->with('success', "Patient transferred successfully to {$destinationBed->room->room_name} - {$destinationBed->bed_number}");
        }

        // Handle Transfer-in process
        if ($newStatus === 'Transfer-in') {
            // Validate source bed
            $sourceBed = Bed::findOrFail($validated['transfer_source_bed_id']);

            if ($sourceBed->status !== 'Transfer-out') {
                return back()->with('error', 'Source bed is not in transfer-out status.');
            }

            // Update bed status to Occupied
            $bed->update([
                'status' => 'Occupied',
                'patient_name' => $sourceBed->patient_name,
                'patient_category' => $sourceBed->patient_category,
                'gender' => $sourceBed->gender,
                'mrn' => $sourceBed->mrn,
                'notes' => $sourceBed->notes,
                'status_changed_at' => Carbon::now(),
            ]);

            return redirect()->route('dashboard')->with('success', "Patient transferred in successfully to {$bed->room->room_name} - {$bed->bed_number}");
        }

        // Handle Discharge process
        if ($newStatus === 'Discharged') {
            // Create a discharge log entry
            DischargeLog::create([
                'bed_id' => $bed->id,
                'room_id' => $bed->room_id,
                'patient_name' => $bed->patient_name ?: $validated['patient_name'],
                'patient_category' => $bed->patient_category ?: $validated['patient_category'],
                'gender' => $bed->gender ?: $validated['gender'],
                'mrn' => $bed->mrn ?: $validated['mrn'],
                'notes' => $bed->notes ?: $validated['notes'],
                'discharged_at' => Carbon::now(),
            ]);

            // Log the discharge activity
            ActivityLogger::log(
                'Discharged Patient',
                "Discharged patient {$bed->patient_name} from {$bed->room->room_name} - {$bed->bed_number}",
                Bed::class,
                $bed->id
            );

            // First record the discharge status with timestamp (for historical purposes)
            $bed->update([
                'status' => 'Discharged',
                'status_changed_at' => Carbon::now(),
            ]);

            // Then immediately set the bed to Housekeeping status
            $bed->update([
                'status' => 'Housekeeping',
                'housekeeping_started_at' => Carbon::now(),
                // Keep patient info for reference until housekeeping is complete
                'status_changed_at' => Carbon::now(),
            ]);

            return redirect()->route('dashboard')->with('success', "Patient discharged and bed {$bed->bed_number} is now in housekeeping.");
        }

        // Handle Housekeeping completion (manually set to Available)
        if ($newStatus === 'Available' && $oldStatus === 'Housekeeping') {
            // Clear patient information when housekeeping is complete
            $validated['patient_name'] = null;
            $validated['patient_category'] = null;
            $validated['gender'] = null;
            $validated['mrn'] = null;
            $validated['notes'] = null;
            $validated['housekeeping_started_at'] = null;
        }
        // Regular status update for non-discharge cases
        // If status is changed to Available, clear patient information
        elseif ($validated['status'] === 'Available') {
            $validated['patient_name'] = null;
            $validated['patient_category'] = null;
            $validated['gender'] = null;
            $validated['mrn'] = null;
            $validated['notes'] = null;
            $validated['housekeeping_started_at'] = null;
        }
        // If status is explicitly set to Housekeeping
        elseif ($validated['status'] === 'Housekeeping') {
            $validated['housekeeping_started_at'] = Carbon::now();
        }

        // Set status_changed_at if status has changed
        if ($oldStatus !== $newStatus) {
            $validated['status_changed_at'] = Carbon::now();
        }

        // Set has_hazard field if present in the request
        if (isset($request->has_hazard)) {
            $bed->has_hazard = $request->has_hazard;
            if (!$request->has_hazard) {
                $bed->hazard_notes = null; // Clear hazard notes when hazard is removed
            } else {
                $bed->hazard_notes = $request->hazard_notes;
            }
        }

        // If status is changed to Available, clear patient information
        if ($validated['status'] === 'Available' && $oldStatus !== 'Available') {
            $validated['patient_name'] = null;
            $validated['patient_category'] = null;
            $validated['gender'] = null;
            $validated['mrn'] = null;
            $validated['notes'] = null;
            $validated['housekeeping_started_at'] = null;
            // Don't clear hazard information as it might be room-specific
        }

        $bed->update($validated);

        // Log the status update activity
        ActivityLogger::log(
            'Updated Bed Status',
            "Changed bed {$bed->bed_number} status from {$oldStatus} to {$newStatus}" .
            ($validated['patient_name'] ? " for patient {$validated['patient_name']}" : ""),
            Bed::class,
            $bed->id
        );

        return redirect()->route('dashboard')->with('success', "Bed {$bed->bed_number} status updated successfully.");
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
