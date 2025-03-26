<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $action The action being performed (e.g., 'created', 'updated')
     * @param string $description Additional details about the action
     * @param string|null $modelType The type of model being acted upon
     * @param int|null $modelId The ID of the model being acted upon
     * @return ActivityLog
     */
    public static function log(string $action, string $description = null, string $modelType = null, int $modelId = null): ActivityLog
    {
        $user = Auth::user();

        $log = ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System',
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecent(int $limit = 10)
    {
        return ActivityLog::with('user')
            ->where('action', '!=', 'Viewed Dashboard')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
