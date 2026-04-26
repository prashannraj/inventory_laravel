<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuppliersSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'ABC Wholesale Suppliers',
                'email' => 'contact@abcwholesale.com',
                'phone' => '01-4423567',
                'tax_number' => 'VAT-123456',
                'address' => 'New Road, Kathmandu',
                'opening_balance' => 50000.00,
                'active' => true,
            ],
            [
                'name' => 'XYZ Electronics',
                'email' => 'info@xyzelectronics.com',
                'phone' => '01-5544332',
                'tax_number' => 'VAT-234567',
                'address' => 'Putalisadak, Kathmandu',
                'opening_balance' => 25000.00,
                'active' => true,
            ],
            [
                'name' => 'Nepal Hardware Store',
                'email' => 'sales@nepalhardware.com',
                'phone' => '01-6655443',
                'tax_number' => 'VAT-345678',
                'address' => 'Kalimati, Kathmandu',
                'opening_balance' => 15000.00,
                'active' => true,
            ],
            [
                'name' => 'Global Textiles',
                'email' => 'orders@globaltextiles.com',
                'phone' => '01-7766554',
                'tax_number' => 'VAT-456789',
                'address' => 'Baneshwor, Kathmandu',
                'opening_balance' => 30000.00,
                'active' => true,
            ],
            [
                'name' => 'Premium Foods Ltd',
                'email' => 'supply@premiumfoods.com',
                'phone' => '01-8877665',
                'tax_number' => 'VAT-567890',
                'address' => 'Bhatbhateni, Kathmandu',
                'opening_balance' => 40000.00,
                'active' => true,
            ],
            [
                'name' => 'Office Supplies Nepal',
                'email' => 'office@suppliesnepal.com',
                'phone' => '01-9988776',
                'tax_number' => 'VAT-678901',
                'address' => 'Dillibazar, Kathmandu',
                'opening_balance' => 20000.00,
                'active' => true,
            ],
            [
                'name' => 'Pharma Distributors',
                'email' => 'distributors@pharma.com',
                'phone' => '01-1122334',
                'tax_number' => 'VAT-789012',
                'address' => 'Teku, Kathmandu',
                'opening_balance' => 60000.00,
                'active' => true,
            ],
            [
                'name' => 'Construction Materials Co.',
                'email' => 'construction@materials.com',
                'phone' => '01-2233445',
                'tax_number' => 'VAT-890123',
                'address' => 'Koteshwor, Kathmandu',
                'opening_balance' => 35000.00,
                'active' => true,
            ],
            [
                'name' => 'Fashion Garments',
                'email' => 'fashion@garments.com',
                'phone' => '01-3344556',
                'tax_number' => 'VAT-901234',
                'address' => 'Thamel, Kathmandu',
                'opening_balance' => 28000.00,
                'active' => true,
            ],
            [
                'name' => 'Tech Gadgets Import',
                'email' => 'import@techgadgets.com',
                'phone' => '01-4455667',
                'tax_number' => 'VAT-012345',
                'address' => 'Maharajgunj, Kathmandu',
                'opening_balance' => 45000.00,
                'active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        $this->command->info('Suppliers seeded successfully!');
    }
}