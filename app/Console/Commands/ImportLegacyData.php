<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Store;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Company;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Hash;

class ImportLegacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:import-legacy-data {--skip-users : Skip importing users} {--skip-products : Skip importing products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from legacy CodeIgniter inventory system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting legacy data import...');
        
        // Check if SQL file exists
        $sqlFile = base_path('../inventory/DATABASE FILE/inventorymgtci.sql');
        if (!file_exists($sqlFile)) {
            $this->error("Legacy SQL file not found at: $sqlFile");
            return 1;
        }
        
        $this->info("Found SQL file: $sqlFile");
        
        // Parse SQL file to extract data
        $sqlContent = file_get_contents($sqlFile);
        
        // Import data table by table
        $this->importBrands($sqlContent);
        $this->importCategories($sqlContent);
        $this->importStores($sqlContent);
        $this->importAttributes($sqlContent);
        $this->importAttributeValues($sqlContent);
        $this->importCompany($sqlContent);
        
        if (!$this->option('skip-users')) {
            $this->importUsers($sqlContent);
        }
        
        if (!$this->option('skip-products')) {
            $this->importProducts($sqlContent);
            $this->importOrders($sqlContent);
        }
        
        $this->info('Legacy data import completed successfully!');
        return 0;
    }
    
    /**
     * Extract INSERT statements for a table from SQL content
     */
    private function extractInsertStatements($sqlContent, $tableName)
    {
        $pattern = '/INSERT INTO `' . $tableName . '`[^;]+;/i';
        preg_match_all($pattern, $sqlContent, $matches);
        
        if (empty($matches[0])) {
            return [];
        }
        
        return $matches[0];
    }
    
    /**
     * Parse INSERT statement into array of rows
     */
    private function parseInsertStatement($insertStatement)
    {
        // Extract values part
        if (preg_match('/VALUES\s*\((.*)\);/is', $insertStatement, $matches)) {
            $valuesPart = $matches[1];
            
            // Split by ),( to get individual rows
            $rows = preg_split('/\)\s*,\s*\(/', $valuesPart);
            
            // Clean up first and last rows
            $rows[0] = preg_replace('/^\(/', '', $rows[0]);
            $rows[count($rows)-1] = preg_replace('/\)$/', '', $rows[count($rows)-1]);
            
            $parsedRows = [];
            foreach ($rows as $row) {
                // Parse row values (simple CSV parsing)
                $values = [];
                $inQuotes = false;
                $currentValue = '';
                
                for ($i = 0; $i < strlen($row); $i++) {
                    $char = $row[$i];
                    
                    if ($char === "'" && ($i === 0 || $row[$i-1] !== '\\')) {
                        $inQuotes = !$inQuotes;
                    } elseif ($char === ',' && !$inQuotes) {
                        $values[] = trim(trim($currentValue), "'");
                        $currentValue = '';
                    } else {
                        $currentValue .= $char;
                    }
                }
                
                if ($currentValue !== '') {
                    $values[] = trim(trim(trim($currentValue), "'"));
                }
                
                $parsedRows[] = $values;
            }
            
            return $parsedRows;
        }
        
        return [];
    }
    
    /**
     * Import brands from legacy data
     */
    private function importBrands($sqlContent)
    {
        $this->info('Importing brands...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'brands');
        if (empty($inserts)) {
            $this->warn('No brands found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 3) {
                Brand::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'name' => $row[1],
                        'active' => $row[2] == '1' ? 1 : 0,
                    ]
                );
                $count++;
            }
        }
        
        $this->info("Imported $count brands");
    }
    
    /**
     * Import categories from legacy data
     */
    private function importCategories($sqlContent)
    {
        $this->info('Importing categories...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'categories');
        if (empty($inserts)) {
            $this->warn('No categories found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 3) {
                Category::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'name' => $row[1],
                        'active' => $row[2] == '1' ? 1 : 0,
                    ]
                );
                $count++;
            }
        }
        
        $this->info("Imported $count categories");
    }
    
    /**
     * Import stores from legacy data
     */
    private function importStores($sqlContent)
    {
        $this->info('Importing stores...');
        
        // Check if stores table exists in SQL
        $inserts = $this->extractInsertStatements($sqlContent, 'stores');
        if (empty($inserts)) {
            $this->warn('No stores found in legacy data, creating default store');
            
            // Create a default store
            Store::updateOrCreate(
                ['id' => 1],
                [
                    'name' => 'Main Warehouse',
                    'location' => 'Headquarters',
                    'active' => 1,
                ]
            );
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 4) {
                Store::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'name' => $row[1],
                        'location' => $row[2] ?? '',
                        'active' => $row[3] == '1' ? 1 : 0,
                    ]
                );
                $count++;
            }
        }
        
        $this->info("Imported $count stores");
    }
    
    /**
     * Import attributes from legacy data
     */
    private function importAttributes($sqlContent)
    {
        $this->info('Importing attributes...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'attributes');
        if (empty($inserts)) {
            $this->warn('No attributes found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 3) {
                Attribute::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'name' => $row[1],
                        'active' => $row[2] == '1' ? 1 : 0,
                    ]
                );
                $count++;
            }
        }
        
        $this->info("Imported $count attributes");
    }
    
    /**
     * Import attribute values from legacy data
     */
    private function importAttributeValues($sqlContent)
    {
        $this->info('Importing attribute values...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'attribute_value');
        if (empty($inserts)) {
            $this->warn('No attribute values found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        $skipped = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 3) {
                $attributeId = $row[2];
                
                // Check if attribute exists
                if (!Attribute::where('id', $attributeId)->exists()) {
                    $this->warn("Skipping attribute value '{$row[1]}' (ID: {$row[0]}) - attribute ID $attributeId does not exist");
                    $skipped++;
                    continue;
                }
                
                AttributeValue::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'value' => $row[1],
                        'attribute_id' => $attributeId,
                    ]
                );
                $count++;
            }
        }
        
        $this->info("Imported $count attribute values, skipped $skipped");
    }
    
    /**
     * Import company from legacy data
     */
    private function importCompany($sqlContent)
    {
        $this->info('Importing company...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'company');
        if (empty($inserts)) {
            $this->warn('No company found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        
        foreach ($rows as $row) {
            if (count($row) >= 9) {
                Company::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'company_name' => $row[1],
                        'service_charge_value' => $row[2],
                        'vat_charge_value' => $row[3],
                        'address' => $row[4],
                        'phone' => $row[5],
                        'country' => $row[6],
                        'message' => $row[7],
                        'currency' => $row[8],
                    ]
                );
                $this->info("Imported company: {$row[1]}");
                break; // Only one company expected
            }
        }
    }
    
    /**
     * Import users from legacy data
     */
    private function importUsers($sqlContent)
    {
        $this->info('Importing users...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'users');
        if (empty($inserts)) {
            $this->warn('No users found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 9) {
                // Map legacy fields to new User model
                $userData = [
                    'id' => $row[0],
                    'username' => $row[1],
                    'email' => !empty($row[2]) ? $row[2] : ($row[1] . '@example.com'),
                    'password' => Hash::make('password123'), // Default password, users should reset
                    'phone' => $row[4] ?? '',
                    'address' => $row[5] ?? '',
                    'status' => $row[6] == '1' ? 'active' : 'inactive',
                    'email_verified_at' => now(),
                ];
                
                // Check if user already exists
                $user = User::find($row[0]);
                if ($user) {
                    $user->update($userData);
                } else {
                    User::create($userData);
                }
                
                $count++;
            }
        }
        
        $this->info("Imported $count users (passwords reset to 'password123')");
    }
    
    /**
     * Import products from legacy data
     */
    private function importProducts($sqlContent)
    {
        $this->info('Importing products...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'products');
        if (empty($inserts)) {
            $this->warn('No products found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 10) {
                $product = Product::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'name' => $row[1],
                        'sku' => $row[2],
                        'price' => $row[3],
                        'qty' => $row[4],
                        'image' => $row[5],
                        'description' => $row[6],
                        'brand_id' => $row[8],
                        'category_id' => $row[9],
                        'store_id' => $row[10] ?? 1,
                        'active' => $row[11] == '1' ? 1 : 0,
                    ]
                );

                // Handle attribute value if present in legacy data
                if (!empty($row[7]) && $row[7] != 'null') {
                    // Check if attribute value exists
                    if (\App\Models\AttributeValue::find($row[7])) {
                        $product->attributeValues()->sync([$row[7]]);
                    }
                }
                $count++;
            }
        }
        
        $this->info("Imported $count products");
    }
    
    /**
     * Import orders from legacy data
     */
    private function importOrders($sqlContent)
    {
        $this->info('Importing orders...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'orders');
        if (empty($inserts)) {
            $this->warn('No orders found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 13) {
                // Convert Unix timestamp to datetime string
                $dateTime = trim($row[5]);
                if (is_numeric($dateTime)) {
                    $dateTime = date('Y-m-d H:i:s', (int) $dateTime);
                }
                
                // Map paid_status: legacy uses 1 for paid, 0 for unpaid
                $paidStatus = 'unpaid';
                if (isset($row[13])) {
                    $paidStatus = $row[13] == '1' ? 'paid' : 'unpaid';
                }
                
                Order::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'bill_no' => $row[1],
                        'customer_name' => $row[2],
                        'customer_address' => $row[3],
                        'customer_phone' => $row[4],
                        'date_time' => $dateTime,
                        'gross_amount' => $row[6],
                        'service_charge_rate' => $row[7],
                        'service_charge' => $row[8],
                        'vat_charge_rate' => $row[9],
                        'vat_charge' => $row[10],
                        'net_amount' => $row[11],
                        'discount' => $row[12],
                        'paid_status' => $paidStatus,
                        'user_id' => $row[14] ?? 1,
                    ]
                );
                $count++;
            }
        }
        
        $this->info("Imported $count orders");
        
        // Import order items
        $this->importOrderItems($sqlContent);
    }
    
    /**
     * Import order items from legacy data
     */
    private function importOrderItems($sqlContent)
    {
        $this->info('Importing order items...');
        
        $inserts = $this->extractInsertStatements($sqlContent, 'orders_item');
        if (empty($inserts)) {
            $this->warn('No order items found in legacy data');
            return;
        }
        
        $rows = $this->parseInsertStatement($inserts[0]);
        $count = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 7) {
                OrderItem::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'order_id' => $row[1],
                        'product_id' => $row[2],
                        'qty' => $row[3],
                        'rate' => $row[4],
                        'amount' => $row[5],
                    ]
                );
                $count++;
            }
        }
        
        $this->info("Imported $count order items");
    }
}
