<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = ['view_users', 'edit_users', 'delete_users', 'create_users'];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
