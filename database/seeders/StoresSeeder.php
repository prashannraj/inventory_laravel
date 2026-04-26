<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoresSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Main Store - Kathmandu',
                'location' => 'New Road, Kathmandu',
                'active' => true,
            ],
            [
                'name' => 'Branch Store - Lalitpur',
                'location' => 'Patan, Lalitpur',
                'active' => true,
            ],
            [
                'name' => 'Branch Store - Bhaktapur',
                'location' => 'Bhaktapur Durbar Square Area',
                'active' => true,
            ],
            [
                'name' => 'Warehouse - Koteshwor',
                'location' => 'Koteshwor, Kathmandu',
                'active' => true,
            ],
            [
                'name' => 'Outlet - Thamel',
                'location' => 'Thamel, Kathmandu',
                'active' => true,
            ],
            [
                'name' => 'Outlet - Baneshwor',
                'location' => 'Baneshwor, Kathmandu',
                'active' => true,
            ],
            [
                'name' => 'Online Store',
                'location' => 'Virtual/Online',
                'active' => true,
            ],
            [
                'name' => 'Wholesale Center',
                'location' => 'Kalimati, Kathmandu',
                'active' => true,
            ],
            [
                'name' => 'Retail Store - Pokhara',
                'location' => 'Lakeside, Pokhara',
                'active' => true,
            ],
            [
                'name' => 'Retail Store - Chitwan',
                'location' => 'Narayangarh, Chitwan',
                'active' => true,
            ],
        ];

        foreach ($stores as $store) {
            Store::create($store);
        }

        $this->command->info('Stores seeded successfully!');
    }
}