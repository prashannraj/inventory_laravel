<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuppliersSeeder::class,
            CustomersSeeder::class,
            BrandsSeeder::class,
            CategoriesSeeder::class,
            PaymentMethodsSeeder::class,
            StoresSeeder::class,
            TaxRatesSeeder::class,
            UnitsSeeder::class,
            InvoiceTemplatesSeeder::class,
            ProductsSeeder::class,
            PurchasesSeeder::class,
            OrdersSeeder::class,
            SalesSeeder::class,
            AdjustmentsSeeder::class,
            // Uncomment the line below to import legacy data
            // LegacyDataSeeder::class,
        ]);
    }
}
