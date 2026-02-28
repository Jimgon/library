<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Authentication Guard & Password Reset
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'system_users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'system_users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'system_users',
        ],

        'staff' => [
            'driver' => 'session',
            'provider' => 'system_users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'system_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\SystemUser::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Settings
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'system_users' => [
            'provider' => 'system_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */
    'password_timeout' => 10800,

];
