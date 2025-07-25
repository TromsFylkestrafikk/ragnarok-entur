<?php

return [
    /*
    | ------------------------------------------------------------------------
    | Entur API parameters and credentials.
    | ------------------------------------------------------------------------
    |
    | Stop places are imported daily and all existing stops are flushed on first
    | imported URL.
    */
    'api' => [
        'auth_url' => env('ENTUR_AUTH_URL'),
        'client_id' => env('ENTUR_CLIENT_ID'),
        'client_secret' => env('ENTUR_SECRET'),
        'audience' => env('ENTUR_AUDIENCE'),
        'api_path' => trim(env('ENTUR_API_PATH'), '/'),
        'env' => env('ENTUR_ENV', 'dev'),
    ],

    'cleos' => [
        'auth_url' => env('ENTUR_CLEOS_AUTH_URL'),
        'client_id' => env('ENTUR_CLEOS_CLIENT_ID'),
        'client_secret' => env('ENTUR_CLEOS_SECRET'),
        'audience' => env('ENTUR_CLEOS_AUDIENCE'),
        'api_path' => trim(env('ENTUR_CLEOS_API_PATH'), '/'),
        'data_product_version' => env('ENTUR_CLEOS_DATA_PRODUCT_VERSION'),
    ],

    'routedata' => [
        'activation_period' => env('ENTUR_ROUTEDATA_PERIOD', 'P30D'),
    ],

    /*
    | ------------------------------------------------------------------------
    | Array of stop archive URLs to import daily
    | ------------------------------------------------------------------------
    |
    | Stop places are imported daily and all existing stops are flushed on first
    | imported URL.
    */
    'import_stop_archives' => explode(' ', env('ENTUR_STOPS_URLS')),
];
