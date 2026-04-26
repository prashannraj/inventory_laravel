<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\TaxRate;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first IDs of related models
        $brandIds = Brand::pluck('id')->toArray();
        $categoryIds = Category::pluck('id')->toArray();
        $storeIds = Store::pluck('id')->toArray();
        $unitIds = Unit::pluck('id')->toArray();
        $taxRateIds = TaxRate::pluck('id')->toArray();

        // If any are empty, seed them first
        if (empty($brandIds)) {
            $this->call(BrandsSeeder::class);
            $brandIds = Brand::pluck('id')->toArray();
        }
        if (empty($categoryIds)) {
            $this->call(CategoriesSeeder::class);
            $categoryIds = Category::pluck('id')->toArray();
        }
        if (empty($storeIds)) {
            $this->call(StoresSeeder::class);
            $storeIds = Store::pluck('id')->toArray();
        }
        if (empty($unitIds)) {
            $this->call(UnitsSeeder::class);
            $unitIds = Unit::pluck('id')->toArray();
        }
        if (empty($taxRateIds)) {
            $this->call(TaxRatesSeeder::class);
            $taxRateIds = TaxRate::pluck('id')->toArray();
        }

        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'IP15P-001',
                'barcode' => '123456789012',
                'price' => 149999.00,
                'buying_price' => 130000.00,
                'qty' => 25,
                'alert_quantity' => 5,
                'image' => null,
                'description' => 'Latest Apple iPhone with advanced camera',
                'brand_id' => $brandIds[0] ?? 1, // Apple
                'category_id' => $categoryIds[2] ?? 3, // Mobile Phones
                'store_id' => $storeIds[0] ?? 1,
                'unit_id' => $unitIds[0] ?? 1, // Piece
                'tax_rate_id' => $taxRateIds[0] ?? 1, // VAT 13%
                'active' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'sku' => 'SGS24-001',
                'barcode' => '123456789013',
                'price' => 129999.00,
                'buying_price' => 110000.00,
                'qty' => 30,
                'alert_quantity' => 5,
                'image' => null,
                'description' => 'Samsung flagship smartphone',
                'brand_id' => $brandIds[1] ?? 2, // Samsung
                'category_id' => $categoryIds[2] ?? 3,
                'store_id' => $storeIds[0] ?? 1,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Dell XPS 15 Laptop',
                'sku' => 'DXP15-001',
                'barcode' => '123456789014',
                'price' => 199999.00,
                'buying_price' => 175000.00,
                'qty' => 15,
                'alert_quantity' => 3,
                'image' => null,
                'description' => 'High-performance laptop for professionals',
                'brand_id' => $brandIds[2] ?? 3, // Dell
                'category_id' => $categoryIds[1] ?? 2, // Computers & Laptops
                'store_id' => $storeIds[0] ?? 1,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Nike Air Max Shoes',
                'sku' => 'NAMS-001',
                'barcode' => '123456789015',
                'price' => 12999.00,
                'buying_price' => 9000.00,
                'qty' => 50,
                'alert_quantity' => 10,
                'image' => null,
                'description' => 'Comfortable running shoes',
                'brand_id' => $brandIds[7] ?? 8, // Nike
                'category_id' => $categoryIds[6] ?? 7, // Footwear
                'store_id' => $storeIds[1] ?? 2,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Coca-Cola 1.5L',
                'sku' => 'CC15-001',
                'barcode' => '123456789016',
                'price' => 120.00,
                'buying_price' => 85.00,
                'qty' => 200,
                'alert_quantity' => 50,
                'image' => null,
                'description' => 'Carbonated soft drink',
                'brand_id' => $brandIds[13] ?? 14, // Coca-Cola
                'category_id' => $categoryIds[8] ?? 9, // Beverages
                'store_id' => $storeIds[2] ?? 3,
                'unit_id' => $unitIds[3] ?? 4, // Liter
                'tax_rate_id' => $taxRateIds[1] ?? 2, // VAT 0%
                'active' => true,
            ],
            [
                'name' => 'HP LaserJet Printer',
                'sku' => 'HPLJ-001',
                'barcode' => '123456789017',
                'price' => 24999.00,
                'buying_price' => 20000.00,
                'qty' => 12,
                'alert_quantity' => 3,
                'image' => null,
                'description' => 'Monochrome laser printer',
                'brand_id' => $brandIds[3] ?? 4, // HP
                'category_id' => $categoryIds[34] ?? 35, // Printers & Scanners
                'store_id' => $storeIds[0] ?? 1,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'LG 55" Smart TV',
                'sku' => 'LG55TV-001',
                'barcode' => '123456789018',
                'price' => 89999.00,
                'buying_price' => 75000.00,
                'qty' => 8,
                'alert_quantity' => 2,
                'image' => null,
                'description' => '4K UHD Smart Television',
                'brand_id' => $brandIds[6] ?? 7, // LG
                'category_id' => $categoryIds[40] ?? 41, // TV & Video
                'store_id' => $storeIds[0] ?? 1,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Unilever Dove Soap',
                'sku' => 'DOVE-001',
                'barcode' => '123456789019',
                'price' => 150.00,
                'buying_price' => 100.00,
                'qty' => 300,
                'alert_quantity' => 50,
                'image' => null,
                'description' => 'Beauty bar soap',
                'brand_id' => $brandIds[11] ?? 12, // Unilever
                'category_id' => $categoryIds[10] ?? 11, // Personal Care
                'store_id' => $storeIds[3] ?? 4,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[1] ?? 2,
                'active' => true,
            ],
            [
                'name' => 'Microsoft Office 365',
                'sku' => 'MSO365-001',
                'barcode' => '123456789020',
                'price' => 9999.00,
                'buying_price' => 7000.00,
                'qty' => 40,
                'alert_quantity' => 10,
                'image' => null,
                'description' => 'Productivity software suite',
                'brand_id' => $brandIds[15] ?? 16, // Microsoft
                'category_id' => $categoryIds[32] ?? 33, // Software
                'store_id' => $storeIds[0] ?? 1,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Canon EOS R5 Camera',
                'sku' => 'CER5-001',
                'barcode' => '123456789021',
                'price' => 299999.00,
                'buying_price' => 250000.00,
                'qty' => 5,
                'alert_quantity' => 1,
                'image' => null,
                'description' => 'Professional mirrorless camera',
                'brand_id' => $brandIds[19] ?? 20, // Canon
                'category_id' => $categoryIds[30] ?? 31, // Cameras & Photography
                'store_id' => $storeIds[0] ?? 1,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Rice 5kg Bag',
                'sku' => 'RICE5-001',
                'barcode' => '123456789022',
                'price' => 850.00,
                'buying_price' => 650.00,
                'qty' => 100,
                'alert_quantity' => 20,
                'image' => null,
                'description' => 'Basmati rice',
                'brand_id' => null,
                'category_id' => $categoryIds[7] ?? 8, // Groceries
                'store_id' => $storeIds[3] ?? 4,
                'unit_id' => $unitIds[1] ?? 2, // Kilogram
                'tax_rate_id' => $taxRateIds[1] ?? 2,
                'active' => true,
            ],
            [
                'name' => 'Adidas T-Shirt',
                'sku' => 'ATSH-001',
                'barcode' => '123456789023',
                'price' => 1999.00,
                'buying_price' => 1200.00,
                'qty' => 80,
                'alert_quantity' => 15,
                'image' => null,
                'description' => 'Cotton sports t-shirt',
                'brand_id' => $brandIds[8] ?? 9, // Adidas
                'category_id' => $categoryIds[5] ?? 6, // Clothing
                'store_id' => $storeIds[1] ?? 2,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Intel Core i7 Processor',
                'sku' => 'ICI7-001',
                'barcode' => '123456789024',
                'price' => 34999.00,
                'buying_price' => 28000.00,
                'qty' => 20,
                'alert_quantity' => 5,
                'image' => null,
                'description' => 'Desktop CPU',
                'brand_id' => $brandIds[17] ?? 18, // Intel
                'category_id' => $categoryIds[1] ?? 2,
                'store_id' => $storeIds[0] ?? 1,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Philips Hair Dryer',
                'sku' => 'PHD-001',
                'barcode' => '123456789025',
                'price' => 2999.00,
                'buying_price' => 2000.00,
                'qty' => 25,
                'alert_quantity' => 5,
                'image' => null,
                'description' => '2000W hair dryer',
                'brand_id' => $brandIds[21] ?? 22, // Philips
                'category_id' => $categoryIds[3] ?? 4, // Home Appliances
                'store_id' => $storeIds[1] ?? 2,
                'unit_id' => $unitIds[0] ?? 1,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
            [
                'name' => 'Toyota Car Oil 1L',
                'sku' => 'TCO1-001',
                'barcode' => '123456789026',
                'price' => 1200.00,
                'buying_price' => 900.00,
                'qty' => 60,
                'alert_quantity' => 10,
                'image' => null,
                'description' => 'Engine oil for cars',
                'brand_id' => $brandIds[28] ?? 29, // Toyota
                'category_id' => $categoryIds[17] ?? 18, // Automotive
                'store_id' => $storeIds[7] ?? 8,
                'unit_id' => $unitIds[3] ?? 4,
                'tax_rate_id' => $taxRateIds[0] ?? 1,
                'active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Products seeded successfully!');
    }
}