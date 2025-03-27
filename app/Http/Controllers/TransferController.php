<?php

namespace App\Http\Controllers;

use App\Models\TransferLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
