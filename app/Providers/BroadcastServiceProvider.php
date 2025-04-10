<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\Fixes\LogManagerFix;
use Pusher\Pusher;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Pusher as a singleton if it's being used
        if (config('broadcasting.default') === 'pusher') {
            $this->registerPusher();
        }
        
        // Set broadcasting routes with explicit cross-domain support
        Broadcast::routes(['middleware' => ['web', 'auth']]);

        require base_path('routes/channels.php');
    }
    
    /**
     * Register Pusher as a singleton
     */
    protected function registerPusher(): void
    {
        $this->app->singleton(Pusher::class, function () {
            $config = config('broadcasting.connections.pusher');
            
            $pusher = new Pusher(
                $config['key'],
                $config['secret'],
                $config['app_id'],
                $config['options'] ?? [],
                $config['client_options'] ?? []
            );
            
            // Enable debugging on the Pusher instance if we're in local environment
            if (config('app.debug')) {
                // Create a custom logger wrapper to prevent null values
                $pusher->setLogger(LogManagerFix::getLogger());
            }
            
            return $pusher;
        });
    }
} 