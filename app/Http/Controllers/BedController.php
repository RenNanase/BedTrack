<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\DischargeLog;
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
        return view('beds.show', compact('bed'));
    }

    /**
     * Update the bed status.
     */
    public function updateStatus(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Available', 'Booked', 'Occupied', 'Discharged'])],
            'patient_name' => 'nullable|required_unless:status,Available|string|max:255',
            'patient_info' => 'nullable|string',
        ]);

        $oldStatus = $bed->status;
        $newStatus = $validated['status'];

        // Handle Discharge process
        if ($newStatus === 'Discharged') {
            // Create a discharge log entry
            DischargeLog::create([
                'bed_id' => $bed->id,
                'room_id' => $bed->room_id,
                'patient_name' => $bed->patient_name ?: $validated['patient_name'],
                'patient_info' => $bed->patient_info ?: $validated['patient_info'],
                'discharged_at' => Carbon::now(),
            ]);

            // First record the discharge status with timestamp (for historical purposes)
            $bed->update([
                'status' => 'Discharged',
                'status_changed_at' => Carbon::now(),
            ]);

            // Then immediately make the bed available
            $bed->update([
                'status' => 'Available',
                'patient_name' => null,
                'patient_info' => null,
                // Don't update status_changed_at for this automatic change
            ]);

            return redirect()->route('dashboard')->with('success', "Patient discharged and bed {$bed->bed_number} is now available.");
        }

        // Regular status update for non-discharge cases
        // If status is changed to Available, clear patient information
        if ($validated['status'] === 'Available') {
            $validated['patient_name'] = null;
            $validated['patient_info'] = null;
        }

        // Set status_changed_at if status has changed
        if ($oldStatus !== $newStatus) {
            $validated['status_changed_at'] = Carbon::now();
        }

        $bed->update($validated);

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
            'patient_info' => 'nullable|string',
        ]);

        $bed->update($validated);

        return redirect()->route('beds.show', $bed)->with('success', 'Patient information updated successfully.');
    }
}
