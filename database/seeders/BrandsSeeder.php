<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple', 'active' => true],
            ['name' => 'Samsung', 'active' => true],
            ['name' => 'Dell', 'active' => true],
            ['name' => 'HP', 'active' => true],
            ['name' => 'Lenovo', 'active' => true],
            ['name' => 'Sony', 'active' => true],
            ['name' => 'LG', 'active' => true],
            ['name' => 'Nike', 'active' => true],
            ['name' => 'Adidas', 'active' => true],
            ['name' => 'Puma', 'active' => true],
            ['name' => 'Reebok', 'active' => true],
            ['name' => 'Unilever', 'active' => true],
            ['name' => 'Nestlé', 'active' => true],
            ['name' => 'Coca-Cola', 'active' => true],
            ['name' => 'Pepsi', 'active' => true],
            ['name' => 'Microsoft', 'active' => true],
            ['name' => 'Google', 'active' => true],
            ['name' => 'Intel', 'active' => true],
            ['name' => 'AMD', 'active' => true],
            ['name' => 'Canon', 'active' => true],
            ['name' => 'Nikon', 'active' => true],
            ['name' => 'Philips', 'active' => true],
            ['name' => 'Panasonic', 'active' => true],
            ['name' => 'Toshiba', 'active' => true],
            ['name' => 'Hitachi', 'active' => true],
            ['name' => 'Whirlpool', 'active' => true],
            ['name' => 'Bosch', 'active' => true],
            ['name' => 'Siemens', 'active' => true],
            ['name' => 'Toyota', 'active' => true],
            ['name' => 'Honda', 'active' => true],
            ['name' => 'Ford', 'active' => true],
            ['name' => 'BMW', 'active' => true],
            ['name' => 'Mercedes', 'active' => true],
            ['name' => 'Audi', 'active' => true],
            ['name' => 'Volkswagen', 'active' => true],
            ['name' => 'Hyundai', 'active' => true],
            ['name' => 'Kia', 'active' => true],
            ['name' => 'Mitsubishi', 'active' => true],
            ['name' => 'Suzuki', 'active' => true],
            ['name' => 'Nokia', 'active' => true],
            ['name' => 'OnePlus', 'active' => true],
            ['name' => 'Xiaomi', 'active' => true],
            ['name' => 'Oppo', 'active' => true],
            ['name' => 'Vivo', 'active' => true],
            ['name' => 'Realme', 'active' => true],
            ['name' => 'Asus', 'active' => true],
            ['name' => 'Acer', 'active' => true],
            ['name' => 'MSI', 'active' => true],
            ['name' => 'Logitech', 'active' => true],
            ['name' => 'Razer', 'active' => true],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }

        $this->command->info('Brands seeded successfully!');
    }
}