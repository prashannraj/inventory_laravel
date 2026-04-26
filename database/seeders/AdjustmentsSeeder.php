<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AdjustmentsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $storeIds = Store::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

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

        $adjustments = [
            [
                'adjustment_no' => 'ADJ-2026-001',
                'store_id' => $storeIds[0] ?? 1,
                'date' => Carbon::now()->subDays(7),
                'reason' => 'Year-end stock count discrepancy',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'adjustment_no' => 'ADJ-2026-002',
                'store_id' => $storeIds[1] ?? 2,
                'date' => Carbon::now()->subDays(5),
                'reason' => 'Damaged goods write-off',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'adjustment_no' => 'ADJ-2026-003',
                'store_id' => $storeIds[2] ?? 3,
                'date' => Carbon::now()->subDays(3),
                'reason' => 'Theft/loss adjustment',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'adjustment_no' => 'ADJ-2026-004',
                'store_id' => $storeIds[3] ?? 4,
                'date' => Carbon::now()->subDays(2),
                'reason' => 'Expired products disposal',
                'user_id' => $userIds[0] ?? 1,
            ],
            [
                'adjustment_no' => 'ADJ-2026-005',
                'store_id' => $storeIds[0] ?? 1,
                'date' => Carbon::now()->subDays(1),
                'reason' => 'Found extra stock',
                'user_id' => $userIds[0] ?? 1,
            ],
        ];

        foreach ($adjustments as $adjustmentData) {
            $adjustment = StockAdjustment::create($adjustmentData);

            // Add adjustment items
            $items = [];
            $numItems = rand(1, 4);
            for ($i = 0; $i < $numItems; $i++) {
                $productId = $productIds[array_rand($productIds)];
                $product = Product::find($productId);
                $quantity = rand(-20, 20); // Can be negative (reduction) or positive (addition)
                $currentQty = $product->qty ?? 0;
                $newQty = max(0, $currentQty + $quantity);
                $type = $quantity >= 0 ? 'addition' : 'reduction';

                $items[] = [
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $productId,
                    'quantity' => $quantity, // Keep signed value (positive for addition, negative for deduction)
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Update product quantity
                $product->qty = $newQty;
                $product->save();
            }

            StockAdjustmentItem::insert($items);
        }

        $this->command->info('Stock adjustments seeded successfully!');
    }
}