<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lynk.id Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Lynk.id payment gateway.
    | Dokumentasi: https://documenter.getpostman.com/view/43601478/2sBXc8o3kn
    |
    */

    'merchant_key' => env('LYNK_MERCHANT_KEY'),

    'is_production' => env('LYNK_IS_PRODUCTION', false),

    'webhook_url' => env('APP_URL').'/api/v1/payments/linkid/webhook',

];
