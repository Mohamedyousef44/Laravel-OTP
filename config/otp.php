<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OTP Repository
    |--------------------------------------------------------------------------
    |
    | Here you can define which repository to use for storing OTPs.
    | Options: "cache", "db"
    |
    */

    'repository' => 'cache', // or 'db'

    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    */
    'length' => 6,                     // OTP length
    'type' => 'numeric',               // numeric | alphanumeric
    'secret_key' => env('OTP_SECRET', 'your-secret-key'),
    'expires_in' => 3,                 // minutes
    'max_attempts' => 3,               // max validation attempts
];
