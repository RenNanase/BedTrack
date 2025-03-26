<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Load more activity logs for AJAX requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadMore(Request $request)
    {
        // Validate the request
        $request->validate([
            'offset' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1|max:20',
        ]);

        $offset = $request->input('offset');
        $limit = $request->input('limit');

        // Get more activity logs
        $logs = ActivityLog::with('user')
            ->where('action', '!=', 'Viewed Dashboard')
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        // Check if there are more logs after the ones we just retrieved
        $moreLogsExist = ActivityLog::where('action', '!=', 'Viewed Dashboard')
            ->orderBy('created_at', 'desc')
            ->skip($offset + $limit)
            ->take(1)
            ->exists();

        // Return the logs as HTML partials
        return response()->json([
            'html' => view('partials.activity-logs', ['activityLogs' => $logs])->render(),
            'hasMore' => $moreLogsExist
        ]);
    }
}
