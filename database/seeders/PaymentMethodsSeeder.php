<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Cash',
                'type' => 'cash',
                'gateway' => null,
                'details' => 'Physical cash payment',
                'active' => true,
            ],
            [
                'name' => 'Credit Card',
                'type' => 'card',
                'gateway' => null,
                'details' => 'Visa, MasterCard, etc.',
                'active' => true,
            ],
            [
                'name' => 'Debit Card',
                'type' => 'card',
                'gateway' => null,
                'details' => 'Bank debit cards',
                'active' => true,
            ],
            [
                'name' => 'Bank Transfer',
                'type' => 'bank',
                'gateway' => null,
                'details' => 'Direct bank transfer',
                'active' => true,
            ],
            [
                'name' => 'Cheque',
                'type' => 'cheque',
                'gateway' => null,
                'details' => 'Payment by cheque',
                'active' => true,
            ],
            [
                'name' => 'eSewa',
                'type' => 'online',
                'gateway' => 'esewa',
                'details' => json_encode([
                    'merchant_code' => env('ESEWA_MERCHANT_CODE', 'EPAYTEST'),
                    'secret_key' => env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q'),
                    'payment_url' => env('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'),
                    'verification_url' => env('ESEWA_VERIFICATION_URL', 'https://uat.esewa.com.np/api/epay/transaction/status/'),
                    'success_url' => env('ESEWA_SUCCESS_URL', 'http://localhost:8000/esewa/success'),
                    'failure_url' => env('ESEWA_FAILURE_URL', 'http://localhost:8000/esewa/failure'),
                ]),
                'active' => true,
            ],
            [
                'name' => 'Khalti',
                'type' => 'online',
                'gateway' => 'khalti',
                'details' => json_encode([
                    'secret_key' => env('KHALTI_SECRET_KEY', ''),
                    'public_key' => env('KHALTI_PUBLIC_KEY', ''),
                    'secret_key_test' => env('KHALTI_SECRET_KEY_TEST', 'test_secret_key_XXXX'),
                    'public_key_test' => env('KHALTI_PUBLIC_KEY_TEST', 'test_public_key_XXXX'),
                    'base_url' => env('KHALTI_BASE_URL', 'https://a.khalti.com/api/v2/'),
                    'initiate_url' => env('KHALTI_INITIATE_URL', 'https://a.khalti.com/api/v2/epayment/initiate/'),
                    'lookup_url' => env('KHALTI_LOOKUP_URL', 'https://a.khalti.com/api/v2/epayment/lookup/'),
                    'return_url' => env('KHALTI_RETURN_URL', 'http://localhost:8000/khalti/callback'),
                    'website_url' => env('KHALTI_WEBSITE_URL', 'http://localhost:8000'),
                    'test_mode' => env('KHALTI_TEST_MODE', true),
                ]),
                'active' => true,
            ],
            [
                'name' => 'Digital Wallet',
                'type' => 'wallet',
                'gateway' => null,
                'details' => 'PayPal, Google Pay, etc.',
                'active' => true,
            ],
            [
                'name' => 'Credit',
                'type' => 'credit',
                'gateway' => null,
                'details' => 'Pay later/on account',
                'active' => true,
            ],
            [
                'name' => 'Cryptocurrency',
                'type' => 'crypto',
                'gateway' => null,
                'details' => 'Bitcoin, Ethereum, etc.',
                'active' => true,
            ],
            [
                'name' => 'Gift Card',
                'type' => 'gift',
                'gateway' => null,
                'details' => 'Store gift cards',
                'active' => true,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }

        $this->command->info('Payment methods seeded successfully!');
    }
}