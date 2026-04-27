<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Khalti Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Khalti payment gateway.
    | Use environment variables for sensitive data.
    |
    */

    // Live credentials
    'secret_key' => env('KHALTI_SECRET_KEY', ''),
    'public_key' => env('KHALTI_PUBLIC_KEY', ''),

    // Test credentials (for sandbox)
    'secret_key_test' => env('KHALTI_SECRET_KEY_TEST', 'test_secret_key_XXXX'),
    'public_key_test' => env('KHALTI_PUBLIC_KEY_TEST', 'test_public_key_XXXX'),

    // Base URLs
    'base_url' => env('KHALTI_BASE_URL', 'https://a.khalti.com/api/v2/'),
    'initiate_url' => env('KHALTI_INITIATE_URL', 'https://a.khalti.com/api/v2/epayment/initiate/'),
    'lookup_url' => env('KHALTI_LOOKUP_URL', 'https://a.khalti.com/api/v2/epayment/lookup/'),

    // Return URLs
    'return_url' => env('KHALTI_RETURN_URL', 'http://localhost:8000/khalti/callback'),
    'website_url' => env('KHALTI_WEBSITE_URL', 'http://localhost:8000'),

    /*
    |--------------------------------------------------------------------------
    | Environment Mode
    |--------------------------------------------------------------------------
    |
    | Determine whether to use test or live environment.
    | Set to true for sandbox/testing, false for production.
    |
    */
    'test_mode' => env('KHALTI_TEST_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    |
    | Khalti expects amount in paisa (1 NPR = 100 paisa).
    |
    */
    'currency' => 'NPR',
    'currency_multiplier' => 100, // Multiply NPR amount by this to get paisa

    /*
    |--------------------------------------------------------------------------
    | Additional Configuration
    |--------------------------------------------------------------------------
    |
    | You can add custom configuration here.
    |
    */
    'timeout' => 30, // Request timeout in seconds
    'verify_ssl' => true, // Verify SSL certificate
];