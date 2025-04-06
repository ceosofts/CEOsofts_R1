<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Domain\Organization\Models\Company;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // สร้าง system-wide permissions
        $this->createSystemPermissions();
        
        // สร้าง system roles
        $this->createSystemRoles();
        
        // สร้าง company-specific roles
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createCompanyRoles($company->id);
        }
    }

    private function createSystemPermissions()
    {
        $permissions = [
            // Companies
            ['name' => 'manage_companies', 'group' => 'system'],
            ['name' => 'view_companies', 'group' => 'system'],
            
            // Users
            ['name' => 'manage_users', 'group' => 'system'],
            ['name' => 'view_users', 'group' => 'system'],
            
            // Roles & Permissions
            ['name' => 'manage_roles', 'group' => 'system'],
            ['name' => 'view_roles', 'group' => 'system'],
            
            // HR Management
            ['name' => 'manage_employees', 'group' => 'hr'],
            ['name' => 'view_employees', 'group' => 'hr'],
            ['name' => 'manage_departments', 'group' => 'hr'],
            ['name' => 'approve_leaves', 'group' => 'hr'],
            
            // Sales
            ['name' => 'manage_customers', 'group' => 'sales'],
            ['name' => 'view_customers', 'group' => 'sales'],
            ['name' => 'create_quotations', 'group' => 'sales'],
            ['name' => 'approve_quotations', 'group' => 'sales'],
            
            // Financial
            ['name' => 'manage_invoices', 'group' => 'finance'],
            ['name' => 'view_invoices', 'group' => 'finance'],
            ['name' => 'manage_payments', 'group' => 'finance'],
            ['name' => 'view_reports', 'group' => 'finance'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => $permission['name'],
                    'guard_name' => 'web'
                ],
                [
                    'group' => $permission['group']
                ]
            );
        }
    }

    private function createSystemRoles()
    {
        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(
            [
                'name' => 'superadmin',
                'guard_name' => 'web'
            ],
            ['level' => 100]
        );
        $superAdmin->syncPermissions(Permission::all());

        // System Admin - can manage companies and users
        $admin = Role::firstOrCreate(
            [
                'name' => 'admin',
                'guard_name' => 'web'
            ],
            ['level' => 90]
        );
        $admin->syncPermissions([
            'manage_companies',
            'view_companies',
            'manage_users',
            'view_users',
        ]);
    }

    private function createCompanyRoles($companyId)
    {
        // Company Admin
        $companyAdmin = Role::firstOrCreate(
            [
                'name' => "company_admin_{$companyId}",
                'guard_name' => 'web'
            ],
            [
                'company_id' => $companyId,
                'level' => 80,
            ]
        );
        $companyAdmin->syncPermissions([
            'manage_employees',
            'view_employees',
            'manage_departments',
            'approve_leaves',
            'manage_customers',
            'view_customers',
            'manage_invoices',
            'view_reports',
        ]);

        // HR Manager
        $hrManager = Role::firstOrCreate(
            [
                'name' => "hr_manager_{$companyId}",
                'guard_name' => 'web'
            ],
            [
                'company_id' => $companyId,
                'level' => 70,
            ]
        );
        $hrManager->syncPermissions([
            'manage_employees',
            'view_employees',
            'manage_departments',
            'approve_leaves',
        ]);

        // Sales Manager
        $salesManager = Role::firstOrCreate(
            [
                'name' => "sales_manager_{$companyId}",
                'guard_name' => 'web'
            ],
            [
                'company_id' => $companyId,
                'level' => 70,
            ]
        );
        $salesManager->syncPermissions([
            'manage_customers',
            'view_customers',
            'create_quotations',
            'approve_quotations',
        ]);

        // Employee
        $employee = Role::firstOrCreate(
            [
                'name' => "employee_{$companyId}",
                'guard_name' => 'web'
            ],
            [
                'company_id' => $companyId,
                'level' => 10,
            ]
        );
        $employee->syncPermissions([
            'view_employees',
        ]);
    }
}
