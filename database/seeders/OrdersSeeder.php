<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrdersSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        if (empty($userIds)) {
            User::factory()->create();
            $userIds = User::pluck('id')->toArray();
        }

        if (empty($productIds)) {
            $this->call(ProductsSeeder::class);
            $productIds = Product::pluck('id')->toArray();
        }

        $orders = [
            [
                'bill_no' => 'ORD-2026-001',
                'customer_name' => 'John Sharma',
                'customer_address' => 'Baneshwor, Kathmandu',
                'customer_phone' => '9841000001',
                'date_time' => Carbon::now()->subDays(5),
                'gross_amount' => 45000.00,
                'service_charge_rate' => 10.00,
                'service_charge' => 4500.00,
                'vat_charge_rate' => 13.00,
                'vat_charge' => 5850.00,
                'net_amount' => 55350.00,
                'discount' => 2000.00,
                'paid_status' => 'paid',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'bill_no' => 'ORD-2026-002',
                'customer_name' => 'Sarah Gurung',
                'customer_address' => 'Lalitpur, Nepal',
                'customer_phone' => '9841000002',
                'date_time' => Carbon::now()->subDays(4),
                'gross_amount' => 28000.00,
                'service_charge_rate' => 10.00,
                'service_charge' => 2800.00,
                'vat_charge_rate' => 13.00,
                'vat_charge' => 3640.00,
                'net_amount' => 34440.00,
                'discount' => 1000.00,
                'paid_status' => 'paid',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'bill_no' => 'ORD-2026-003',
                'customer_name' => 'Rajesh Enterprises',
                'customer_address' => 'New Road, Kathmandu',
                'customer_phone' => '01-4433221',
                'date_time' => Carbon::now()->subDays(3),
                'gross_amount' => 125000.00,
                'service_charge_rate' => 10.00,
                'service_charge' => 12500.00,
                'vat_charge_rate' => 13.00,
                'vat_charge' => 16250.00,
                'net_amount' => 153750.00,
                'discount' => 5000.00,
                'paid_status' => 'partial',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'bill_no' => 'ORD-2026-004',
                'customer_name' => 'Maya Thapa',
                'customer_address' => 'Bhaktapur, Nepal',
                'customer_phone' => '9841000004',
                'date_time' => Carbon::now()->subDays(2),
                'gross_amount' => 15000.00,
                'service_charge_rate' => 10.00,
                'service_charge' => 1500.00,
                'vat_charge_rate' => 13.00,
                'vat_charge' => 1950.00,
                'net_amount' => 18450.00,
                'discount' => 500.00,
                'paid_status' => 'paid',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'bill_no' => 'ORD-2026-005',
                'customer_name' => 'Hotel Mountain View',
                'customer_address' => 'Thamel, Kathmandu',
                'customer_phone' => '01-6655443',
                'date_time' => Carbon::now()->subDays(1),
                'gross_amount' => 85000.00,
                'service_charge_rate' => 10.00,
                'service_charge' => 8500.00,
                'vat_charge_rate' => 13.00,
                'vat_charge' => 11050.00,
                'net_amount' => 104550.00,
                'discount' => 3000.00,
                'paid_status' => 'not_paid',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'bill_no' => 'ORD-2026-006',
                'customer_name' => 'Anita Shrestha',
                'customer_address' => 'Koteshwor, Kathmandu',
                'customer_phone' => '9841000006',
                'date_time' => Carbon::now()->subHours(12),
                'gross_amount' => 22000.00,
                'service_charge_rate' => 10.00,
                'service_charge' => 2200.00,
                'vat_charge_rate' => 13.00,
                'vat_charge' => 2860.00,
                'net_amount' => 27060.00,
                'discount' => 800.00,
                'paid_status' => 'paid',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'bill_no' => 'ORD-2026-007',
                'customer_name' => 'Bikash Rai',
                'customer_address' => 'Dharan, Nepal',
                'customer_phone' => '9841000008',
                'date_time' => Carbon::now()->subHours(6),
                'gross_amount' => 18000.00,
                'service_charge_rate' => 10.00,
                'service_charge' => 1800.00,
                'vat_charge_rate' => 13.00,
                'vat_charge' => 2340.00,
                'net_amount' => 22140.00,
                'discount' => 600.00,
                'paid_status' => 'paid',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'bill_no' => 'ORD-2026-008',
                'customer_name' => 'Restaurant Taste of Nepal',
                'customer_address' => 'Bhatbhateni, Kathmandu',
                'customer_phone' => '01-8877665',
                'date_time' => Carbon::now()->subHours(3),
                'gross_amount' => 65000.00,
                'service_charge_rate' => 10.00,
                'service_charge' => 6500.00,
                'vat_charge_rate' => 13.00,
                'vat_charge' => 8450.00,
                'net_amount' => 79950.00,
                'discount' => 2000.00,
                'paid_status' => 'partial',
                'user_id' => $userIds[0] ?? 1,
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);

            // Add order items
            $items = [];
            $numItems = rand(1, 4);
            for ($i = 0; $i < $numItems; $i++) {
                $productId = $productIds[array_rand($productIds)];
                $product = Product::find($productId);
                $quantity = rand(1, 5);
                $rate = $product->price ?? rand(500, 20000);
                $total = $quantity * $rate;

                $items[] = [
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'total' => $total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            OrderItem::insert($items);
        }

        $this->command->info('Orders seeded successfully!');
    }
}