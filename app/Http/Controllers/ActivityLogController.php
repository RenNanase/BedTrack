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
        // Validate request
        $request->validate([
            'offset' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1|max:20',
        ]);

        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $wardId = session('selected_ward_id');

        // Get activities from activity_logs table (bed activities)
        $bedActivities = \App\Models\ActivityLog::with('user')
            ->where('action', '!=', 'Viewed Dashboard')
            ->when($wardId, function ($query) use ($wardId) {
                return $query->where('ward_id', $wardId);
            })
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        // Get activities from activity_log table (bassinet activities)
        $bassinetActivities = \Spatie\Activitylog\Models\Activity::query()
            ->where(function($query) {
                $query->where('log_name', 'default')
                      ->orWhere('description', 'LIKE', '%bassinet%')
                      ->orWhere('description', 'LIKE', '%baby%');
            })
            ->where(function($query) use ($wardId) {
                $query->whereJsonContains('properties->ward_id', $wardId)
                    ->orWhereJsonContains('properties->attributes->ward_id', $wardId)
                    ->orWhereJsonContains('properties->attributes->room.ward_id', $wardId)
                    ->orWhereJsonContains('properties->ward.id', $wardId)
                    ->orWhereJsonContains('properties->room.ward_id', $wardId)
                    ->orWhere(function($q) use ($wardId) {
                        $q->where('properties->ward_id', $wardId);
                    })
                    ->orWhere(function($q) use ($wardId) {
                        $q->where('description', 'LIKE', '%bassinet%')
                          ->where('description', 'LIKE', '%ward_id:'.$wardId.'%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        // Combine the collections and re-sort
        $combinedLogs = $bedActivities->merge($bassinetActivities)
            ->sortByDesc('created_at')
            ->values()
            ->take($limit);

        // Pre-process activities to ensure they display correctly
        $processedLogs = $combinedLogs->map(function ($activity) {
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

        // Check if there are more logs 
        $hasMore = app(\App\Services\ActivityLogger::class)->hasMoreLogs($offset + $limit, 1, $wardId);

        // Generate HTML for each log item individually
        $html = '';
        foreach ($processedLogs as $log) {
            $html .= view('partials._activity_log_item', ['log' => $log])->render();
        }

        // Return logs as HTML partials
        return response()->json([
            'html' => $html,
            'hasMore' => $hasMore
        ]);
    }
}
