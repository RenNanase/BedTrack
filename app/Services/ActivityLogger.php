<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

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

        return ActivityLog::with('user')
            ->where('action', '!=', 'Viewed Dashboard')
            ->when($wardId, function ($query) use ($wardId) {
                return $query->where('ward_id', $wardId);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
