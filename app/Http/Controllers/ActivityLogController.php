<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Ward;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activityLogs = ActivityLog::with(['user', 'bed.room.ward'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $wards = Ward::all();
        $hasMoreLogs = ActivityLog::count() > 5;

        return view('activity-logs.index', compact('activityLogs', 'wards', 'hasMoreLogs'));
    }

    /**
     * Load more activity logs for AJAX requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadMore(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 5);
        $action = $request->input('action');
        $ward = $request->input('ward');

        $query = ActivityLog::with(['user', 'bed.room.ward'])
            ->orderBy('created_at', 'desc');

        if ($action) {
            $query->where('action', $action);
        }

        if ($ward) {
            $query->whereHas('bed.room', function ($q) use ($ward) {
                $q->where('ward_id', $ward);
            });
        }

        $activityLogs = $query->skip($offset)
            ->take($limit)
            ->get();

        $hasMoreLogs = $query->count() > ($offset + $limit);

        $html = view('partials.activity-logs', compact('activityLogs'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hasMoreLogs
        ]);
    }
}
