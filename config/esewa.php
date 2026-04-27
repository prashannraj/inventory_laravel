<?php

return [
    /*
    |--------------------------------------------------------------------------
    | eSewa Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for eSewa payment gateway.
    | Use environment variables for sensitive data.
    |
    */

    'merchant_code' => env('ESEWA_MERCHANT_CODE', 'EPAYTEST'),
    'secret_key' => env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q'),
    'payment_url' => env('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'),
    'verification_url' => env('ESEWA_VERIFICATION_URL', 'https://uat.esewa.com.np/api/epay/transaction/status/'),
    'success_url' => env('ESEWA_SUCCESS_URL', 'http://localhost:8000/esewa/success'),
    'failure_url' => env('ESEWA_FAILURE_URL', 'http://localhost:8000/esewa/failure'),

    /*
    |--------------------------------------------------------------------------
    | Tax and Service Charge Configuration
    |--------------------------------------------------------------------------
    |
    | These values are used for calculating tax and service charges.
    | You can adjust them as per your business requirements.
    |
    */
    'tax_amount' => 0, // Tax amount in NPR (set to 0 if not applicable)
    'product_service_charge' => 0, // Service charge in NPR
    'product_delivery_charge' => 0, // Delivery charge in NPR

    /*
    |--------------------------------------------------------------------------
    | Signature Configuration
    |--------------------------------------------------------------------------
    |
    | The signature is generated using HMAC SHA256 algorithm.
    | The message format is: total_amount={amount},transaction_uuid={uuid},product_code={merchant_code}
    |
    */
    'signature_algorithm' => 'sha256',
    'signed_field_names' => 'total_amount,transaction_uuid,product_code',

    /*
    |--------------------------------------------------------------------------
    | Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Use test credentials for sandbox environment.
    |
    */
    'test_mode' => env('ESEWA_TEST_MODE', true),
];