<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'active' => true],
            ['name' => 'Computers & Laptops', 'active' => true],
            ['name' => 'Mobile Phones', 'active' => true],
            ['name' => 'Home Appliances', 'active' => true],
            ['name' => 'Furniture', 'active' => true],
            ['name' => 'Clothing', 'active' => true],
            ['name' => 'Footwear', 'active' => true],
            ['name' => 'Groceries', 'active' => true],
            ['name' => 'Beverages', 'active' => true],
            ['name' => 'Snacks', 'active' => true],
            ['name' => 'Personal Care', 'active' => true],
            ['name' => 'Health & Wellness', 'active' => true],
            ['name' => 'Beauty Products', 'active' => true],
            ['name' => 'Stationery', 'active' => true],
            ['name' => 'Books', 'active' => true],
            ['name' => 'Toys & Games', 'active' => true],
            ['name' => 'Sports Equipment', 'active' => true],
            ['name' => 'Automotive', 'active' => true],
            ['name' => 'Tools & Hardware', 'active' => true],
            ['name' => 'Building Materials', 'active' => true],
            ['name' => 'Office Supplies', 'active' => true],
            ['name' => 'Electrical', 'active' => true],
            ['name' => 'Plumbing', 'active' => true],
            ['name' => 'Gardening', 'active' => true],
            ['name' => 'Pet Supplies', 'active' => true],
            ['name' => 'Baby Products', 'active' => true],
            ['name' => 'Jewelry', 'active' => true],
            ['name' => 'Watches', 'active' => true],
            ['name' => 'Luggage & Bags', 'active' => true],
            ['name' => 'Musical Instruments', 'active' => true],
            ['name' => 'Cameras & Photography', 'active' => true],
            ['name' => 'Gaming', 'active' => true],
            ['name' => 'Software', 'active' => true],
            ['name' => 'Networking', 'active' => true],
            ['name' => 'Printers & Scanners', 'active' => true],
            ['name' => 'Monitors', 'active' => true],
            ['name' => 'Storage Devices', 'active' => true],
            ['name' => 'Computer Accessories', 'active' => true],
            ['name' => 'Mobile Accessories', 'active' => true],
            ['name' => 'TV & Video', 'active' => true],
            ['name' => 'Audio & Headphones', 'active' => true],
            ['name' => 'Kitchen Appliances', 'active' => true],
            ['name' => 'Cleaning Supplies', 'active' => true],
            ['name' => 'Laundry', 'active' => true],
            ['name' => 'Lighting', 'active' => true],
            ['name' => 'Fans & Cooling', 'active' => true],
            ['name' => 'Heating', 'active' => true],
            ['name' => 'Security Systems', 'active' => true],
            ['name' => 'CCTV', 'active' => true],
            ['name' => 'Solar Products', 'active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Categories seeded successfully!');
    }
}