<?php

namespace App\Http\Controllers;

use App\Models\TransferLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bed;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $selectedWardId = session('selected_ward_id');
        $type = $request->get('type', 'all'); // 'in', 'out', or 'all'
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = TransferLog::with([
            'sourceBed.room.ward',
            'destinationBed.room.ward'
        ]);

        // Filter by ward
        if ($type === 'in') {
            $query->whereHas('destinationBed.room.ward', function($q) use ($selectedWardId) {
                $q->where('id', $selectedWardId);
            });
        } elseif ($type === 'out') {
            $query->whereHas('sourceBed.room.ward', function($q) use ($selectedWardId) {
                $q->where('id', $selectedWardId);
            });
        } else {
            $query->where(function($q) use ($selectedWardId) {
                $q->whereHas('sourceBed.room.ward', function($q) use ($selectedWardId) {
                    $q->where('id', $selectedWardId);
                })->orWhereHas('destinationBed.room.ward', function($q) use ($selectedWardId) {
                    $q->where('id', $selectedWardId);
                });
            });
        }

        // Search by patient name or MRN
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('mrn', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('transferred_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('transferred_at', '<=', $dateTo);
        }

        // Order by transfer date
        $query->orderBy('transferred_at', 'desc');

        // Paginate results
        $transfers = $query->paginate(15);

        return view('transfers.index', compact('transfers', 'type', 'search', 'dateFrom', 'dateTo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_bed_id' => 'required|exists:beds,id',
            'destination_bed_id' => 'required|exists:beds,id',
            'patient_name' => 'required|string|max:255',
            'patient_category' => 'required|string|max:255',
            'gender' => 'required|string|max:10',
            'mrn' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        try {
            // Get source and destination beds
            $sourceBed = Bed::findOrFail($validated['source_bed_id']);
            $destinationBed = Bed::findOrFail($validated['destination_bed_id']);

            // Check if destination room is blocked
            if ($destinationBed->room->is_blocked) {
                return back()->with('error', 'Cannot transfer to a blocked room.');
            }

            // Check if destination ward is blocked
            if ($destinationBed->room->ward->is_blocked) {
                return back()->with('error', 'Cannot transfer to a blocked ward.');
            }

            // Check if source bed is in Transfer-out status
            if ($sourceBed->status !== 'Transfer-out') {
                return back()->with('error', 'Source bed must be in Transfer-out status.');
            }

            // Check if destination bed is Available
            if ($destinationBed->status !== 'Available') {
                return back()->with('error', 'Destination bed must be available.');
            }

            // Start database transaction
            \DB::beginTransaction();

            // Update source bed
            $sourceBed->update([
                'status' => 'Available',
                'patient_name' => null,
                'patient_category' => null,
                'gender' => null,
                'mrn' => null,
                'notes' => null,
                'status_changed_at' => now(),
            ]);

            // Update destination bed
            $destinationBed->update([
                'status' => 'Occupied',
                'patient_name' => $validated['patient_name'],
                'patient_category' => $validated['patient_category'],
                'gender' => $validated['gender'],
                'mrn' => $validated['mrn'],
                'notes' => $validated['notes'],
                'status_changed_at' => now(),
            ]);

            // Create transfer log
            TransferLog::create([
                'source_bed_id' => $sourceBed->id,
                'destination_bed_id' => $destinationBed->id,
                'patient_name' => $validated['patient_name'],
                'patient_category' => $validated['patient_category'],
                'gender' => $validated['gender'],
                'mrn' => $validated['mrn'],
                'notes' => $validated['notes'],
                'transferred_at' => now(),
            ]);

            \DB::commit();

            return redirect()->route('dashboard')->with('success', 'Patient transferred successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to transfer patient: ' . $e->getMessage());
        }
    }
}
