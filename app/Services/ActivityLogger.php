<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\Models\Activity;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $action The action being performed (e.g., 'created', 'updated')
     * @param string $description Additional details about the action
     * @param string|null $modelType The type of model being acted upon
     * @param int|null $modelId The ID of the model being acted upon
     * @param int|null $wardId The ID of the ward context (defaults to session selected ward)
     * @return ActivityLog
     */
    public static function log(string $action, string $description = null, string $modelType = null, int $modelId = null, int $wardId = null): ActivityLog
    {
        $user = Auth::user();

        // If ward ID is not provided, use the currently selected ward from the session
        if ($wardId === null) {
            $wardId = session('selected_ward_id');
        }

        $log = ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System',
            'ward_id' => $wardId,
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => Request::ip(),
        ]);

        return $log;
    }

    /**
     * Get recent activity logs.
     *
     * @param int $limit Number of logs to return
     * @param int|null $wardId The ID of the ward to filter logs by (defaults to session selected ward)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecent(int $limit = 10, int $wardId = null)
    {
        // If ward ID is not provided, use the currently selected ward from the session
        if ($wardId === null) {
            $wardId = session('selected_ward_id');
        }

        // Get activities from activity_logs table (bed activities)
        $bedActivities = ActivityLog::with('user')
            ->where('action', '!=', 'Viewed Dashboard')
            ->when($wardId, function ($query) use ($wardId) {
                return $query->where('ward_id', $wardId);
            })
            ->orderBy('created_at', 'desc');

        // Get activities from activity_log table (bassinet activities)
        $bassinetActivities = Activity::query()
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
            ->orderBy('created_at', 'desc');

        // Combine both activity sources with a union and get the most recent activities
        try {
            $combinedActivities = $bedActivities->unionAll($bassinetActivities)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            // If union fails, use the alternative approach
            $combinedActivities = collect();
        }

        // If union doesn't work (depends on DB compatibility), use this alternative approach
        if ($combinedActivities->isEmpty()) {
            $bedActivities = $bedActivities->limit($limit)->get();
            $bassinetActivities = $bassinetActivities->limit($limit)->get();
            
            // Convert both collections to arrays and combine them
            $allActivities = $bedActivities->toBase()->merge($bassinetActivities);
            
            // Sort by created_at
            $combinedActivities = $allActivities->sortByDesc('created_at')->take($limit);
        }

        return $combinedActivities;
    }

    /**
     * Get more activity logs with pagination.
     *
     * @param int $offset Offset for pagination
     * @param int $limit Limit for pagination
     * @param int|null $wardId The ID of the ward to filter logs by
     * @return \Illuminate\Support\Collection
     */
    public static function getMoreLogs(int $offset = 0, int $limit = 10, int $wardId = null)
    {
        // If ward ID is not provided, use the currently selected ward from the session
        if ($wardId === null) {
            $wardId = session('selected_ward_id');
        }

        // Get activities from activity_logs table (bed activities)
        $bedActivities = ActivityLog::with('user')
            ->where('action', '!=', 'Viewed Dashboard')
            ->when($wardId, function ($query) use ($wardId) {
                return $query->where('ward_id', $wardId);
            })
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit);

        // Get activities from activity_log table (bassinet activities)
        $bassinetActivities = Activity::query()
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
            ->take($limit);

        // Combine both activity sources
        try {
            $combinedActivities = $bedActivities->unionAll($bassinetActivities)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            // If union fails, use the alternative approach
            $combinedActivities = collect();
        }

        // If union doesn't work, use alternative approach
        if ($combinedActivities->isEmpty()) {
            $bedActivities = $bedActivities->get();
            $bassinetActivities = $bassinetActivities->get();
            
            // Convert both collections to arrays and combine them
            $allActivities = $bedActivities->toBase()->merge($bassinetActivities);
            
            // Sort by created_at and paginate
            $combinedActivities = $allActivities->sortByDesc('created_at')
                ->slice($offset, $limit);
        }

        return $combinedActivities;
    }

    /**
     * Check if there are more logs available after the offset.
     *
     * @param int $offset Offset for pagination
     * @param int $limit Limit for pagination
     * @param int|null $wardId The ID of the ward to filter logs by
     * @return bool
     */
    public static function hasMoreLogs(int $offset = 0, int $limit = 1, int $wardId = null): bool
    {
        // If ward ID is not provided, use the currently selected ward from the session
        if ($wardId === null) {
            $wardId = session('selected_ward_id');
        }

        // Check if there are more bed activities
        $moreBedActivities = ActivityLog::where('action', '!=', 'Viewed Dashboard')
            ->when($wardId, function ($query) use ($wardId) {
                return $query->where('ward_id', $wardId);
            })
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->exists();

        // Check if there are more bassinet activities
        $moreBassinetActivities = Activity::query()
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
            ->exists();

        // Return true if there are more activities in either table
        return $moreBedActivities || $moreBassinetActivities;
    }
}
