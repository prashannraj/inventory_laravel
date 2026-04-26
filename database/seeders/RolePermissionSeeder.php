<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all possible permissions from legacy system
        $legacyPermissions = [
            'createUser', 'updateUser', 'viewUser', 'deleteUser',
            'createGroup', 'updateGroup', 'viewGroup', 'deleteGroup',
            'createBrand', 'updateBrand', 'viewBrand', 'deleteBrand',
            'createCategory', 'updateCategory', 'viewCategory', 'deleteCategory',
            'createStore', 'updateStore', 'viewStore', 'deleteStore',
            'createAttribute', 'updateAttribute', 'viewAttribute', 'deleteAttribute',
            'createProduct', 'updateProduct', 'viewProduct', 'deleteProduct',
            'createOrder', 'updateOrder', 'viewOrder', 'deleteOrder',
            'createSupplier', 'updateSupplier', 'viewSupplier', 'deleteSupplier',
            'createCustomer', 'updateCustomer', 'viewCustomer', 'deleteCustomer',
            'createPurchase', 'updatePurchase', 'viewPurchase', 'deletePurchase',
            'createSale', 'updateSale', 'viewSale', 'deleteSale',
            'createAdjustment', 'updateAdjustment', 'viewAdjustment', 'deleteAdjustment',
            'createUnit', 'updateUnit', 'viewUnit', 'deleteUnit',
            'createTaxRate', 'updateTaxRate', 'viewTaxRate', 'deleteTaxRate',
            'viewReports', 'updateCompany', 'viewProfile', 'updateSetting',
            'manageStock', 'viewCashFlow', 'managePayments', 'manageTemplates', 'manageOrders'
        ];

        // Create permissions
        foreach ($legacyPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles with their permissions (mapped from legacy groups)
        $roles = [
            'Administrator' => $legacyPermissions, // All permissions
            'Testing' => [
                'updateUser', 'viewUser', 'createGroup', 'updateGroup', 'viewGroup',
                'createBrand', 'updateBrand', 'viewBrand', 'createCategory', 'updateCategory',
                'viewCategory', 'createStore', 'updateStore', 'viewStore', 'createAttribute',
                'updateAttribute', 'viewAttribute', 'createProduct', 'updateProduct', 'viewProduct',
                'createOrder', 'updateOrder', 'viewOrder', 'updateCompany'
            ],
            'Employee' => [
                'viewUser', 'createBrand', 'updateBrand', 'viewBrand', 'createProduct',
                'updateProduct', 'viewProduct', 'createOrder', 'updateOrder', 'viewOrder'
            ]
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }

        // Create admin user
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@inventory.com',
                'password' => bcrypt('admin123'),
                'status' => 'active'
            ]
        );

        $admin->assignRole('Administrator');

        // Create test employee user
        $employee = User::firstOrCreate(
            ['username' => 'employee'],
            [
                'name' => 'John Employee',
                'email' => 'employee@inventory.com',
                'password' => bcrypt('employee123'),
                'status' => 'active'
            ]
        );

        $employee->assignRole('Employee');
    }
}
