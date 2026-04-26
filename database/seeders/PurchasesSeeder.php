<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PurchasesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $supplierIds = Supplier::pluck('id')->toArray();
        $storeIds = Store::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        if (empty($supplierIds) || empty($storeIds) || empty($userIds) || empty($productIds)) {
            $this->command->warn('Required data missing for purchases. Seeding suppliers, stores, users, products first.');
            $this->call([
                SuppliersSeeder::class,
                StoresSeeder::class,
                ProductsSeeder::class,
            ]);
            
            // Re-fetch IDs
            $supplierIds = Supplier::pluck('id')->toArray();
            $storeIds = Store::pluck('id')->toArray();
            $productIds = Product::pluck('id')->toArray();
            
            // Create a user if none exists
            if (empty($userIds)) {
                User::factory()->create();
                $userIds = User::pluck('id')->toArray();
            }
        }

        $purchases = [
            [
                'purchase_no' => 'PUR-2026-001',
                'supplier_id' => $supplierIds[0] ?? 1,
                'store_id' => $storeIds[0] ?? 1,
                'date' => Carbon::now()->subDays(10),
                'total_amount' => 450000.00,
                'discount' => 10000.00,
                'tax_amount' => 57200.00,
                'net_amount' => 497200.00,
                'status' => 'completed',
                'notes' => 'Bulk order for electronics',
                'document' => null,
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'purchase_no' => 'PUR-2026-002',
                'supplier_id' => $supplierIds[1] ?? 2,
                'store_id' => $storeIds[1] ?? 2,
                'date' => Carbon::now()->subDays(7),
                'total_amount' => 125000.00,
                'discount' => 5000.00,
                'tax_amount' => 15600.00,
                'net_amount' => 135600.00,
                'status' => 'completed',
                'notes' => 'Mobile phones stock',
                'document' => null,
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'purchase_no' => 'PUR-2026-003',
                'supplier_id' => $supplierIds[2] ?? 3,
                'store_id' => $storeIds[2] ?? 3,
                'date' => Carbon::now()->subDays(5),
                'total_amount' => 85000.00,
                'discount' => 2000.00,
                'tax_amount' => 10790.00,
                'net_amount' => 93790.00,
                'status' => 'pending',
                'notes' => 'Waiting for delivery',
                'document' => null,
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'purchase_no' => 'PUR-2026-004',
                'supplier_id' => $supplierIds[3] ?? 4,
                'store_id' => $storeIds[3] ?? 4,
                'date' => Carbon::now()->subDays(3),
                'total_amount' => 32000.00,
                'discount' => 0.00,
                'tax_amount' => 4160.00,
                'net_amount' => 36160.00,
                'status' => 'completed',
                'notes' => 'Office supplies',
                'document' => null,
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'purchase_no' => 'PUR-2026-005',
                'supplier_id' => $supplierIds[4] ?? 5,
                'store_id' => $storeIds[0] ?? 1,
                'date' => Carbon::now()->subDays(1),
                'total_amount' => 180000.00,
                'discount' => 15000.00,
                'tax_amount' => 21450.00,
                'net_amount' => 186450.00,
                'status' => 'completed',
                'notes' => 'Food items for cafeteria',
                'document' => null,
                'user_id' => $userIds[0] ?? 1,
            ],
        ];

        foreach ($purchases as $purchaseData) {
            $purchase = Purchase::create($purchaseData);

            // Add purchase items
            $items = [];
            $numItems = rand(2, 5);
            for ($i = 0; $i < $numItems; $i++) {
                $productId = $productIds[array_rand($productIds)];
                $product = Product::find($productId);
                $quantity = rand(5, 50);
                $unitPrice = $product->buying_price ?? rand(100, 10000);
                $total = $quantity * $unitPrice;

                $items[] = [
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            PurchaseItem::insert($items);
        }

        $this->command->info('Purchases seeded successfully!');
    }
}