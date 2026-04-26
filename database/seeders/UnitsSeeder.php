<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'short_name' => 'pc', 'active' => true],
            ['name' => 'Kilogram', 'short_name' => 'kg', 'active' => true],
            ['name' => 'Gram', 'short_name' => 'g', 'active' => true],
            ['name' => 'Liter', 'short_name' => 'L', 'active' => true],
            ['name' => 'Milliliter', 'short_name' => 'ml', 'active' => true],
            ['name' => 'Meter', 'short_name' => 'm', 'active' => true],
            ['name' => 'Centimeter', 'short_name' => 'cm', 'active' => true],
            ['name' => 'Dozen', 'short_name' => 'doz', 'active' => true],
            ['name' => 'Pack', 'short_name' => 'pack', 'active' => true],
            ['name' => 'Box', 'short_name' => 'box', 'active' => true],
            ['name' => 'Carton', 'short_name' => 'ctn', 'active' => true],
            ['name' => 'Pair', 'short_name' => 'pair', 'active' => true],
            ['name' => 'Set', 'short_name' => 'set', 'active' => true],
            ['name' => 'Bottle', 'short_name' => 'bottle', 'active' => true],
            ['name' => 'Can', 'short_name' => 'can', 'active' => true],
            ['name' => 'Bag', 'short_name' => 'bag', 'active' => true],
            ['name' => 'Sack', 'short_name' => 'sack', 'active' => true],
            ['name' => 'Roll', 'short_name' => 'roll', 'active' => true],
            ['name' => 'Sheet', 'short_name' => 'sheet', 'active' => true],
            ['name' => 'Unit', 'short_name' => 'unit', 'active' => true],
            ['name' => 'Foot', 'short_name' => 'ft', 'active' => true],
            ['name' => 'Inch', 'short_name' => 'in', 'active' => true],
            ['name' => 'Yard', 'short_name' => 'yd', 'active' => true],
            ['name' => 'Square Meter', 'short_name' => 'sq m', 'active' => true],
            ['name' => 'Square Foot', 'short_name' => 'sq ft', 'active' => true],
            ['name' => 'Cubic Meter', 'short_name' => 'cu m', 'active' => true],
            ['name' => 'Gallon', 'short_name' => 'gal', 'active' => true],
            ['name' => 'Quintal', 'short_name' => 'q', 'active' => true],
            ['name' => 'Ton', 'short_name' => 'ton', 'active' => true],
            ['name' => 'Packet', 'short_name' => 'pkt', 'active' => true],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }

        $this->command->info('Units seeded successfully!');
    }
}