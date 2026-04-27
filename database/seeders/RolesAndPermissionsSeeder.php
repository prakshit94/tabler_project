<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'CREATE_ORDER',
            'VIEW_REPORT',
            'MANAGE_USERS',
            'MANAGE_ROLES',
            'MANAGE_PERMISSIONS',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']); // if using api guard occasionally
        }

        // 2. Create Roles and assign existing permissions
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $roleAdmin->syncPermissions(Permission::where('guard_name', 'web')->get()); // Admin gets all

        $roleManager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $roleManager->givePermissionTo(['CREATE_ORDER', 'VIEW_REPORT', 'MANAGE_USERS']);

        $roleAgent = Role::firstOrCreate(['name' => 'Call Agent', 'guard_name' => 'web']);
        $roleAgent->givePermissionTo(['CREATE_ORDER', 'VIEW_REPORT']);

        $roleWarehouse = Role::firstOrCreate(['name' => 'Warehouse Staff', 'guard_name' => 'web']);
        $roleWarehouse->givePermissionTo(['VIEW_REPORT']);

        $roleDelivery = Role::firstOrCreate(['name' => 'Delivery Agent', 'guard_name' => 'web']);
        $roleDelivery->givePermissionTo(['VIEW_REPORT']); // example limited access

        // 3. Create Dummy Users and assign roles
        
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@agricrm.test'],
            [
                'name' => 'System Admin',
                'mobile' => '1000000001',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $admin->assignRole($roleAdmin);

        // Manager User
        $manager = User::firstOrCreate(
            ['email' => 'manager@agricrm.test'],
            [
                'name' => 'Regional Manager',
                'mobile' => '1000000002',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $manager->assignRole($roleManager);

        // Call Agent User
        $agent = User::firstOrCreate(
            ['email' => 'agent@agricrm.test'],
            [
                'name' => 'Sales Agent',
                'mobile' => '1000000003',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $agent->assignRole($roleAgent);

        // Warehouse User
        $warehouse = User::firstOrCreate(
            ['email' => 'warehouse@agricrm.test'],
            [
                'name' => 'Inventory Manager',
                'mobile' => '1000000004',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $warehouse->assignRole($roleWarehouse);

        // Delivery User
        $delivery = User::firstOrCreate(
            ['email' => 'delivery@agricrm.test'],
            [
                'name' => 'Driver John',
                'mobile' => '1000000005',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $delivery->assignRole($roleDelivery);
    }
}
