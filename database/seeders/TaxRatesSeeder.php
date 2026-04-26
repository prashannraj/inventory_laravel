<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxRatesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxRates = [
            [
                'name' => 'VAT 13%',
                'rate' => 13.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 0% (Zero Rated)',
                'rate' => 0.00,
                'active' => true,
            ],
            [
                'name' => 'Exempt',
                'rate' => 0.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 5% (Special Category)',
                'rate' => 5.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 15% (Luxury Goods)',
                'rate' => 15.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 20% (Alcohol/Tobacco)',
                'rate' => 20.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 25% (Special Excise)',
                'rate' => 25.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 30% (High Luxury)',
                'rate' => 30.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 1.5% (Telecom)',
                'rate' => 1.50,
                'active' => true,
            ],
            [
                'name' => 'VAT 10% (Hotel Services)',
                'rate' => 10.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 2% (Education)',
                'rate' => 2.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 4% (Agriculture)',
                'rate' => 4.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 6% (Healthcare)',
                'rate' => 6.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 8% (Construction)',
                'rate' => 8.00,
                'active' => true,
            ],
            [
                'name' => 'VAT 12% (General)',
                'rate' => 12.00,
                'active' => true,
            ],
        ];

        foreach ($taxRates as $taxRate) {
            TaxRate::create($taxRate);
        }

        $this->command->info('Tax rates seeded successfully!');
    }
}