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
                'details' => 'Physical cash payment',
                'active' => true,
            ],
            [
                'name' => 'Credit Card',
                'type' => 'card',
                'details' => 'Visa, MasterCard, etc.',
                'active' => true,
            ],
            [
                'name' => 'Debit Card',
                'type' => 'card',
                'details' => 'Bank debit cards',
                'active' => true,
            ],
            [
                'name' => 'Bank Transfer',
                'type' => 'bank',
                'details' => 'Direct bank transfer',
                'active' => true,
            ],
            [
                'name' => 'Cheque',
                'type' => 'cheque',
                'details' => 'Payment by cheque',
                'active' => true,
            ],
            [
                'name' => 'Mobile Payment',
                'type' => 'mobile',
                'details' => 'Khalti, eSewa, IME Pay',
                'active' => true,
            ],
            [
                'name' => 'Digital Wallet',
                'type' => 'wallet',
                'details' => 'PayPal, Google Pay, etc.',
                'active' => true,
            ],
            [
                'name' => 'Credit',
                'type' => 'credit',
                'details' => 'Pay later/on account',
                'active' => true,
            ],
            [
                'name' => 'Cryptocurrency',
                'type' => 'crypto',
                'details' => 'Bitcoin, Ethereum, etc.',
                'active' => true,
            ],
            [
                'name' => 'Gift Card',
                'type' => 'gift',
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