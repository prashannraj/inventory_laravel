<?php

namespace Database\Seeders;

use App\Models\InvoiceTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceTemplatesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Standard Invoice',
                'layout' => 'standard',
                'header_text' => 'INVOICE',
                'footer_text' => 'Thank you for your business!',
                'show_logo' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Professional',
                'layout' => 'professional',
                'header_text' => 'TAX INVOICE',
                'footer_text' => 'Terms & Conditions Apply',
                'show_logo' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Minimal',
                'layout' => 'minimal',
                'header_text' => 'BILL',
                'footer_text' => '',
                'show_logo' => false,
                'is_default' => false,
            ],
            [
                'name' => 'Modern',
                'layout' => 'modern',
                'header_text' => 'SALES INVOICE',
                'footer_text' => 'Please pay within 30 days',
                'show_logo' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Classic',
                'layout' => 'classic',
                'header_text' => 'COMMERCIAL INVOICE',
                'footer_text' => 'All prices include VAT where applicable',
                'show_logo' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Receipt',
                'layout' => 'receipt',
                'header_text' => 'RECEIPT',
                'footer_text' => 'Payment Received - Thank You',
                'show_logo' => false,
                'is_default' => false,
            ],
            [
                'name' => 'Nepali Style',
                'layout' => 'nepali',
                'header_text' => 'बिल',
                'footer_text' => 'धन्यवाद!',
                'show_logo' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Service Invoice',
                'layout' => 'service',
                'header_text' => 'SERVICE INVOICE',
                'footer_text' => 'Service provided as per agreement',
                'show_logo' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Wholesale',
                'layout' => 'wholesale',
                'header_text' => 'WHOLESALE INVOICE',
                'footer_text' => 'For wholesale customers only',
                'show_logo' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Retail',
                'layout' => 'retail',
                'header_text' => 'RETAIL INVOICE',
                'footer_text' => 'Goods sold are not returnable',
                'show_logo' => true,
                'is_default' => false,
            ],
        ];

        foreach ($templates as $template) {
            InvoiceTemplate::create($template);
        }

        $this->command->info('Invoice templates seeded successfully!');
    }
}