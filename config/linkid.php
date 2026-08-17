<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Link.id Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Link.id payment gateway.
    | Dokumentasi: https://documenter.getpostman.com/view/43601478/2sBXc8o3kn
    |
    */

    'api_key' => env('LINKID_API_KEY'),

    'is_production' => env('LINKID_IS_PRODUCTION', false),

    'webhook_secret' => env('LINKID_WEBHOOK_SECRET'),

    'callback_url' => env('APP_URL').'/webhook/linkid',

];
