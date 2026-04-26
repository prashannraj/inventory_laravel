<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SalesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerIds = Customer::pluck('id')->toArray();
        $storeIds = Store::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        if (empty($customerIds)) {
            $this->call(CustomersSeeder::class);
            $customerIds = Customer::pluck('id')->toArray();
        }
        if (empty($storeIds)) {
            $this->call(StoresSeeder::class);
            $storeIds = Store::pluck('id')->toArray();
        }
        if (empty($userIds)) {
            User::factory()->create();
            $userIds = User::pluck('id')->toArray();
        }
        if (empty($productIds)) {
            $this->call(ProductsSeeder::class);
            $productIds = Product::pluck('id')->toArray();
        }

        $sales = [
            [
                'invoice_no' => 'INV-2026-001',
                'customer_id' => $customerIds[0] ?? 1,
                'store_id' => $storeIds[0] ?? 1,
                'date' => Carbon::now()->subDays(10),
                'total_amount' => 55000.00,
                'discount' => 2000.00,
                'tax_amount' => 6890.00,
                'net_amount' => 59890.00,
                'paid_amount' => 59890.00,
                'status' => 'completed',
                'payment_status' => 'paid',
                'notes' => 'Walk-in customer',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'invoice_no' => 'INV-2026-002',
                'customer_id' => $customerIds[1] ?? 2,
                'store_id' => $storeIds[1] ?? 2,
                'date' => Carbon::now()->subDays(8),
                'total_amount' => 32000.00,
                'discount' => 1000.00,
                'tax_amount' => 4030.00,
                'net_amount' => 35030.00,
                'paid_amount' => 20000.00,
                'status' => 'completed',
                'payment_status' => 'partial',
                'notes' => 'Partial payment received',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'invoice_no' => 'INV-2026-003',
                'customer_id' => $customerIds[2] ?? 3,
                'store_id' => $storeIds[0] ?? 1,
                'date' => Carbon::now()->subDays(6),
                'total_amount' => 125000.00,
                'discount' => 5000.00,
                'tax_amount' => 15600.00,
                'net_amount' => 135600.00,
                'paid_amount' => 135600.00,
                'status' => 'completed',
                'payment_status' => 'paid',
                'notes' => 'Corporate order',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'invoice_no' => 'INV-2026-004',
                'customer_id' => $customerIds[3] ?? 4,
                'store_id' => $storeIds[2] ?? 3,
                'date' => Carbon::now()->subDays(4),
                'total_amount' => 18000.00,
                'discount' => 500.00,
                'tax_amount' => 2275.00,
                'net_amount' => 19775.00,
                'paid_amount' => 19775.00,
                'status' => 'completed',
                'payment_status' => 'paid',
                'notes' => 'Online order',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'invoice_no' => 'INV-2026-005',
                'customer_id' => $customerIds[4] ?? 5,
                'store_id' => $storeIds[0] ?? 1,
                'date' => Carbon::now()->subDays(2),
                'total_amount' => 75000.00,
                'discount' => 3000.00,
                'tax_amount' => 9360.00,
                'net_amount' => 81360.00,
                'paid_amount' => 0.00,
                'status' => 'completed',
                'payment_status' => 'not_paid',
                'notes' => 'Credit sale',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'invoice_no' => 'INV-2026-006',
                'customer_id' => $customerIds[5] ?? 6,
                'store_id' => $storeIds[1] ?? 2,
                'date' => Carbon::now()->subDays(1),
                'total_amount' => 42000.00,
                'discount' => 1500.00,
                'tax_amount' => 5265.00,
                'net_amount' => 45765.00,
                'paid_amount' => 45765.00,
                'status' => 'completed',
                'payment_status' => 'paid',
                'notes' => 'Cash sale',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'invoice_no' => 'INV-2026-007',
                'customer_id' => $customerIds[6] ?? 7,
                'store_id' => $storeIds[0] ?? 1,
                'date' => Carbon::now()->subHours(12),
                'total_amount' => 95000.00,
                'discount' => 4000.00,
                'tax_amount' => 11830.00,
                'net_amount' => 102830.00,
                'paid_amount' => 50000.00,
                'status' => 'completed',
                'payment_status' => 'partial',
                'notes' => 'Hotel supply',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'invoice_no' => 'INV-2026-008',
                'customer_id' => $customerIds[7] ?? 8,
                'store_id' => $storeIds[3] ?? 4,
                'date' => Carbon::now()->subHours(6),
                'total_amount' => 28000.00,
                'discount' => 800.00,
                'tax_amount' => 3536.00,
                'net_amount' => 30736.00,
                'paid_amount' => 30736.00,
                'status' => 'completed',
                'payment_status' => 'paid',
                'notes' => 'Retail sale',
                'user_id' => $userIds[0] ?? 1,
            ],
        ];

        foreach ($sales as $saleData) {
            $sale = Sale::create($saleData);

            // Add sale items
            $items = [];
            $numItems = rand(1, 5);
            $itemTotal = 0;
            for ($i = 0; $i < $numItems; $i++) {
                $productId = $productIds[array_rand($productIds)];
                $product = Product::find($productId);
                $quantity = rand(1, 10);
                $unitPrice = $product->price ?? rand(500, 20000);
                $total = $quantity * $unitPrice;
                $itemTotal += $total;

                $items[] = [
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            SaleItem::insert($items);

            // Add payment if paid
            if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => 1, // Cash
                    'amount' => $sale->paid_amount,
                    'notes' => 'Payment for invoice ' . $sale->invoice_no,
                    'date' => $sale->date,
                    'user_id' => $sale->user_id,
                ]);
            }
        }

        $this->command->info('Sales history seeded successfully!');
    }
}