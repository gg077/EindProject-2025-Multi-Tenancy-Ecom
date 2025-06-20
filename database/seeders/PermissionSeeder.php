<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    protected $permissions = [
        1 => [ // Tenant Admin
            'web' => [
                ['module' => 'User Management', 'permissions' => ['view user', 'create user', 'update user', 'delete user', 'restore user', 'forceDelete user']],
                ['module' => 'Role Management', 'permissions' => ['view role', 'create role', 'update role', 'delete role']],
                ['module' => 'Category Management', 'permissions' => ['view category', 'create category', 'update category', 'delete category']],
                ['module' => 'Product Management', 'permissions' => ['view product', 'create product', 'update product', 'delete product']],
                ['module' => 'Order', 'permissions' => ['view order']],
                ['module' => 'Website Settings', 'permissions' => ['update website settings']],
            ],
        ],
        2 => [ // Super Admin
            'web' => [
                ['module' => 'User Management', 'permissions' => ['view user', 'create user', 'update user', 'delete user', 'restore user', 'forceDelete user']],
                ['module' => 'Role Management', 'permissions' => ['view role', 'create role', 'update role', 'delete role']],
                ['module' => 'Tenant Management', 'permissions' => ['view tenant', 'create tenant', 'update tenant', 'delete tenant']],
            ],
        ],
    ];

    public function run(): void
    {
        Artisan::call('permission:cache-reset');

        foreach ($this->permissions as $userType => $group) {
            foreach ($group as $guard => $modules) {
                foreach ($modules as $item) {
                    // Create or get the module
                    $module = Module::firstOrCreate([
                        'name' => $item['module'],
                        'user_type' => $userType,
                    ]);

                    // Create or update permissions
                    foreach ($item['permissions'] as $permission) {
                        DB::table('permissions')->updateOrInsert(
                            [
                                'name' => $permission,
                                'guard_name' => $guard,
                                'module_id' => $module->id,
                            ],
                            [
                                'name' => $permission,
                                'guard_name' => $guard,
                                'module_id' => $module->id,
                            ]
                        );
                    }
                }
            }
        }
    }
}