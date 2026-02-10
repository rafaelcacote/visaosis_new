<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cerberus Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for integrating with the Cerberus authentication system
    |
    */

    'url' => env('CERBERUS_URL', 'http://localhost:8000'),
    'system_key' => env('CERBERUS_SYSTEM_KEY', 'visaosis'),
    'timeout' => env('CERBERUS_TIMEOUT', 10),
    'cache_duration' => env('CERBERUS_CACHE_DURATION', 3600), // 1 hour
];
