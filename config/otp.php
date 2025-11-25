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
    |
    | 'length'  : number of characters/digits
    | 'type'    : numeric | alphanumeric
    | 'expires_in' : lifetime of OTP (in minutes)
    | 'max_attempts': how many validation attempts before lockout
    |
    */
    'length' => 6,
    'type' => "numeric",
    'expires_in' => 3,
    'max_attempts' => 3,

    /*
    |--------------------------------------------------------------------------
    | Secret key for HMAC hashing (optional)
    |--------------------------------------------------------------------------
    |
    | If you want HMAC instead of raw hash, set a secret here. If empty,
    | package will use hash_hmac with this key or fallback to application key.
    |
    */
    'secret_key' =>  env('APP_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OTP validations messages
    |--------------------------------------------------------------------------
    |
    */
    'messages' => [
        'valid'   => 'OTP is valid.',
        'expired' => 'OTP has expired.',
        'invalid' => 'OTP is invalid.',
        'not_found' => 'No OTP record found.',
        'max_attempts' => 'Maximum validation attempts reached.'
    ],
];
