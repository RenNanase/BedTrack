<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vite Server URL
    |--------------------------------------------------------------------------
    |
    | This setting specifies the URL of the Vite development server when running
    | in development mode. This URL must be accessible from your web browser.
    |
    */

    'dev_url' => env('VITE_DEV_SERVER_URL', 'http://localhost:5173'),

    /*
    |--------------------------------------------------------------------------
    | Entrypoints
    |--------------------------------------------------------------------------
    |
    | The entrypoints defined in your vite.config.js file are listed here.
    |
    */

    'entrypoints' => [
        'resources/css/app.css',
        'resources/js/app.js',
    ],

    /*
    |--------------------------------------------------------------------------
    | Hot Module Replacement
    |--------------------------------------------------------------------------
    |
    | If hot module replacement is enabled, Vite will automatically reload
    | your changes in the browser. This setting is generally used during
    | development.
    |
    */

    'hmr_enabled' => true,
]; 