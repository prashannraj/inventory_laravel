<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Sharma',
                'email' => 'john.sharma@example.com',
                'phone' => '9841000001',
                'tax_number' => 'PAN-100001',
                'address' => 'Baneshwor, Kathmandu',
                'opening_balance' => 0.00,
                'active' => true,
                'credit_limit' => 50000.00,
                'loyalty_points' => 150,
            ],
            [
                'name' => 'Sarah Gurung',
                'email' => 'sarah.gurung@example.com',
                'phone' => '9841000002',
                'tax_number' => 'PAN-100002',
                'address' => 'Lalitpur, Nepal',
                'opening_balance' => 0.00,
                'active' => true,
                'credit_limit' => 30000.00,
                'loyalty_points' => 75,
            ],
            [
                'name' => 'Rajesh Enterprises',
                'email' => 'rajesh@enterprises.com',
                'phone' => '01-4433221',
                'tax_number' => 'PAN-100003',
                'address' => 'New Road, Kathmandu',
                'opening_balance' => 25000.00,
                'active' => true,
                'credit_limit' => 100000.00,
                'loyalty_points' => 500,
            ],
            [
                'name' => 'Maya Thapa',
                'email' => 'maya.thapa@example.com',
                'phone' => '9841000004',
                'tax_number' => 'PAN-100004',
                'address' => 'Bhaktapur, Nepal',
                'opening_balance' => 0.00,
                'active' => true,
                'credit_limit' => 20000.00,
                'loyalty_points' => 30,
            ],
            [
                'name' => 'Nepal Retail Store',
                'email' => 'store@nepalretail.com',
                'phone' => '01-5544332',
                'tax_number' => 'PAN-100005',
                'address' => 'Putalisadak, Kathmandu',
                'opening_balance' => 15000.00,
                'active' => true,
                'credit_limit' => 75000.00,
                'loyalty_points' => 200,
            ],
            [
                'name' => 'Anita Shrestha',
                'email' => 'anita.shrestha@example.com',
                'phone' => '9841000006',
                'tax_number' => 'PAN-100006',
                'address' => 'Koteshwor, Kathmandu',
                'opening_balance' => 0.00,
                'active' => true,
                'credit_limit' => 15000.00,
                'loyalty_points' => 45,
            ],
            [
                'name' => 'Hotel Mountain View',
                'email' => 'accounts@hotelmountain.com',
                'phone' => '01-6655443',
                'tax_number' => 'PAN-100007',
                'address' => 'Thamel, Kathmandu',
                'opening_balance' => 50000.00,
                'active' => true,
                'credit_limit' => 200000.00,
                'loyalty_points' => 1000,
            ],
            [
                'name' => 'Bikash Rai',
                'email' => 'bikash.rai@example.com',
                'phone' => '9841000008',
                'tax_number' => 'PAN-100008',
                'address' => 'Dharan, Nepal',
                'opening_balance' => 0.00,
                'active' => true,
                'credit_limit' => 25000.00,
                'loyalty_points' => 60,
            ],
            [
                'name' => 'School Supplies Nepal',
                'email' => 'school@suppliesnepal.com',
                'phone' => '01-7766554',
                'tax_number' => 'PAN-100009',
                'address' => 'Dillibazar, Kathmandu',
                'opening_balance' => 10000.00,
                'active' => true,
                'credit_limit' => 50000.00,
                'loyalty_points' => 300,
            ],
            [
                'name' => 'Prabin Tamang',
                'email' => 'prabin.tamang@example.com',
                'phone' => '9841000010',
                'tax_number' => 'PAN-100010',
                'address' => 'Pokhara, Nepal',
                'opening_balance' => 0.00,
                'active' => true,
                'credit_limit' => 10000.00,
                'loyalty_points' => 20,
            ],
            [
                'name' => 'Restaurant Taste of Nepal',
                'email' => 'taste@nepalrestaurant.com',
                'phone' => '01-8877665',
                'tax_number' => 'PAN-100011',
                'address' => 'Bhatbhateni, Kathmandu',
                'opening_balance' => 30000.00,
                'active' => true,
                'credit_limit' => 80000.00,
                'loyalty_points' => 400,
            ],
            [
                'name' => 'Sita Magar',
                'email' => 'sita.magar@example.com',
                'phone' => '9841000012',
                'tax_number' => 'PAN-100012',
                'address' => 'Butwal, Nepal',
                'opening_balance' => 0.00,
                'active' => true,
                'credit_limit' => 12000.00,
                'loyalty_points' => 25,
            ],
            [
                'name' => 'Office Complex Ltd',
                'email' => 'office@complex.com',
                'phone' => '01-9988776',
                'tax_number' => 'PAN-100013',
                'address' => 'Maharajgunj, Kathmandu',
                'opening_balance' => 40000.00,
                'active' => true,
                'credit_limit' => 150000.00,
                'loyalty_points' => 600,
            ],
            [
                'name' => 'Hari Basnet',
                'email' => 'hari.basnet@example.com',
                'phone' => '9841000014',
                'tax_number' => 'PAN-100014',
                'address' => 'Hetauda, Nepal',
                'opening_balance' => 0.00,
                'active' => true,
                'credit_limit' => 8000.00,
                'loyalty_points' => 15,
            ],
            [
                'name' => 'Supermarket Chain Nepal',
                'email' => 'chain@supermarket.com',
                'phone' => '01-1122334',
                'tax_number' => 'PAN-100015',
                'address' => 'Kalimati, Kathmandu',
                'opening_balance' => 75000.00,
                'active' => true,
                'credit_limit' => 300000.00,
                'loyalty_points' => 1500,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $this->command->info('Customers seeded successfully!');
    }
}