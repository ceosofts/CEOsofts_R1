<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Settings\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // สร้าง superadmin user
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@ceosofts.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_system_admin' => true,
                'language' => 'th',
                'timezone' => 'Asia/Bangkok',
            ]
        );

        // กำหนดบทบาท superadmin
        $superadminRole = Role::where('name', 'superadmin')
            ->where('guard_name', 'web')
            ->first();

        if ($superadminRole) {
            $superadmin->syncRoles([$superadminRole]);
        }

        // สร้าง admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@ceosofts.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_system_admin' => false,
                'language' => 'th',
                'timezone' => 'Asia/Bangkok',
            ]
        );

        // กำหนดบทบาท admin
        $adminRole = Role::where('name', 'admin')
            ->where('guard_name', 'web')
            ->first();

        if ($adminRole) {
            $admin->syncRoles([$adminRole]);
        }
    }
}
