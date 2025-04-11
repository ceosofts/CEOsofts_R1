<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Admin', 'Manager', 'Employee'];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
