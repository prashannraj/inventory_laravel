<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdditionalPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'viewTransfer', 'createTransfer', 'updateTransfer', 'deleteTransfer',
            'viewReturn', 'createReturn', 'updateReturn', 'deleteReturn',
            'viewExpense', 'createExpense', 'updateExpense', 'deleteExpense',
            'manageTemplate'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::where('name', 'Administrator')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }
    }
}
