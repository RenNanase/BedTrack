<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register admin middleware
        Route::aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);

        // Register ward selection middleware
        Route::aliasMiddleware('ward.selection', \App\Http\Middleware\WardSelectionMiddleware::class);
    }
}
