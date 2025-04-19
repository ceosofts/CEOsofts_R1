<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบว่ามี roles ที่จำเป็นในระบบหรือยัง
        $this->ensureRolesExist();
        
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@ceosofts.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'superadmin'
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@ceosofts.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin'
            ],
            [
                'name' => 'Test User',
                'email' => 'testuser@ceosofts.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user'
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'] ?? 'user';
            unset($userData['role']);
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // กำหนด role ให้กับ user
            $user->syncRoles([$role]);
        }
    }
    
    /**
     * ตรวจสอบว่ามี roles ที่จำเป็นหรือยัง
     */
    private function ensureRolesExist()
    {
        $roles = ['superadmin', 'admin', 'manager', 'employee', 'user'];
        
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
