<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Name
    |--------------------------------------------------------------------------
    |
    | Here you can define default payment name.
    | 
    | Available: 'kassa24'
    |
    */

    'default' => env('PAYMENT_DEFAULT', 'kassa24'),

    /*
    |--------------------------------------------------------------------------
    | Kassa24 settings
    |--------------------------------------------------------------------------
    | 
    | Define settings for Kassa24 payment.
    |
    | base_url - base url of Kassa24 API
    | login - Kassa24 login
    | password - Kassa24 password
    | merchant_id - Kassa24 merchant id(login)
    | allowed_ips - list of allowed IP addresses, leave it empty if you don't want to restrict
    | 
    | @see https://business.kassa24.kz/documentation/payment-methods
    |
    */

    'kassa24' => [
        'base_url' => env('KASSA24_BASE_URL', 'https://ecommerce.pult24.kz'),
        'login' => env('KASSA24_LOGIN'),
        'password' => env('KASSA24_PASSWORD'),
        'merchant_id' => env('KASSA24_MERCHANT_ID', env('KASSA24_LOGIN')),
        'allowed_ips' => [
            env('KASSA24_CALLBACK_HOST', '35.157.105.64'),
            env('APP_URL', '127.0.0.1:8000'),
        ],
    ],

    'url' => [
        'return' => env('MOBILE_APP_URL', 'https://eyva.kz'),
        'callback' => env('KASSA24_CALLBACK_URL', env('APP_URL') . '/api/v1/payment/status'),
    ],

];
