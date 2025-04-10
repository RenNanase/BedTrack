<?php

namespace App\Fixes;

use Illuminate\Support\Facades\Log;
use App\Fixes\PusherLoggerWrapper;

class LogManagerFix
{
    public static function apply()
    {
        // Ensure we're in PHP 8.3 where this issue occurs
        if (version_compare(PHP_VERSION, '8.3.0', '<')) {
            return;
        }
        
        // Direct patch for the trim() deprecation warning
        // The warning occurs in the LogManager::parseLevel method when null is passed to trim()
        // This error occurs in vendor/laravel/framework/src/Illuminate/Log/LogManager.php on line 631
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            // Only handle deprecation warnings about trim() null parameter
            if (
                $errno === E_DEPRECATED &&
                str_contains($errstr, 'trim()') && 
                str_contains($errstr, 'null') &&
                str_contains($errfile, 'LogManager.php')
            ) {
                // Suppress only this specific warning
                return true;
            }
            // Let PHP handle other errors
            return false;
        }, E_DEPRECATED);
    }

    /**
     * Create a safe logger for Pusher to use
     */
    public static function getLogger()
    {
        return new PusherLoggerWrapper(Log::channel());
    }
} 