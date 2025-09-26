<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'module' => 'user_management'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'module' => 'user_management'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'module' => 'user_management'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'module' => 'user_management'],
            ['name' => 'users.activate', 'display_name' => 'Activate Users', 'module' => 'user_management'],
            ['name' => 'users.deactivate', 'display_name' => 'Deactivate Users', 'module' => 'user_management'],

            // Role Management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'module' => 'role_management'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'module' => 'role_management'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'module' => 'role_management'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'module' => 'role_management'],

            // Permission Management
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'module' => 'permission_management'],
            ['name' => 'permissions.create', 'display_name' => 'Create Permissions', 'module' => 'permission_management'],
            ['name' => 'permissions.edit', 'display_name' => 'Edit Permissions', 'module' => 'permission_management'],
            ['name' => 'permissions.delete', 'display_name' => 'Delete Permissions', 'module' => 'permission_management'],

            // Business Type Management
            ['name' => 'business_types.view', 'display_name' => 'View Business Types', 'module' => 'business_management'],
            ['name' => 'business_types.create', 'display_name' => 'Create Business Types', 'module' => 'business_management'],
            ['name' => 'business_types.edit', 'display_name' => 'Edit Business Types', 'module' => 'business_management'],
            ['name' => 'business_types.delete', 'display_name' => 'Delete Business Types', 'module' => 'business_management'],

            // Module Management
            ['name' => 'modules.view', 'display_name' => 'View Modules', 'module' => 'module_management'],
            ['name' => 'modules.create', 'display_name' => 'Create Modules', 'module' => 'module_management'],
            ['name' => 'modules.edit', 'display_name' => 'Edit Modules', 'module' => 'module_management'],
            ['name' => 'modules.delete', 'display_name' => 'Delete Modules', 'module' => 'module_management'],
            ['name' => 'modules.activate', 'display_name' => 'Activate Modules', 'module' => 'module_management'],

            // Accounting Module
            ['name' => 'accounting.view', 'display_name' => 'View Accounting', 'module' => 'accounting'],
            ['name' => 'accounting.create', 'display_name' => 'Create Accounting Records', 'module' => 'accounting'],
            ['name' => 'accounting.edit', 'display_name' => 'Edit Accounting Records', 'module' => 'accounting'],
            ['name' => 'accounting.delete', 'display_name' => 'Delete Accounting Records', 'module' => 'accounting'],
            ['name' => 'accounting.reports', 'display_name' => 'View Accounting Reports', 'module' => 'accounting'],

            // Mix Design Module
            ['name' => 'mix_designs.view', 'display_name' => 'View Mix Designs', 'module' => 'mix_design'],
            ['name' => 'mix_designs.create', 'display_name' => 'Create Mix Designs', 'module' => 'mix_design'],
            ['name' => 'mix_designs.edit', 'display_name' => 'Edit Mix Designs', 'module' => 'mix_design'],
            ['name' => 'mix_designs.delete', 'display_name' => 'Delete Mix Designs', 'module' => 'mix_design'],

            // System Administration
            ['name' => 'system.settings', 'display_name' => 'Manage System Settings', 'module' => 'system'],
            ['name' => 'system.logs', 'display_name' => 'View System Logs', 'module' => 'system'],
            ['name' => 'system.backup', 'display_name' => 'Manage Backups', 'module' => 'system'],
            ['name' => 'system.maintenance', 'display_name' => 'System Maintenance', 'module' => 'system'],

            // Security
            ['name' => 'security.events', 'display_name' => 'View Security Events', 'module' => 'security'],
            ['name' => 'security.sessions', 'display_name' => 'Manage User Sessions', 'module' => 'security'],
            ['name' => 'security.audit', 'display_name' => 'View Audit Logs', 'module' => 'security'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'is_system' => true,
                'level' => 100,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access to most system features',
                'is_system' => true,
                'level' => 80,
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Management access to business operations',
                'is_system' => true,
                'level' => 60,
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Standard staff access to assigned modules',
                'is_system' => true,
                'level' => 40,
            ],
            [
                'name' => 'auditor',
                'display_name' => 'Auditor',
                'description' => 'Read-only access for auditing purposes',
                'is_system' => true,
                'level' => 30,
            ],
            [
                'name' => 'client',
                'display_name' => 'Client',
                'description' => 'Limited access for external clients',
                'is_system' => true,
                'level' => 20,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );

            // Assign permissions based on role level
            $this->assignPermissionsToRole($role);
        }
    }

    /**
     * Assign permissions to role based on level
     */
    private function assignPermissionsToRole(Role $role)
    {
        $permissions = Permission::all();

        switch ($role->name) {
            case 'super_admin':
                // Super admin gets all permissions
                $role->permissions()->sync($permissions->pluck('id'));
                break;

            case 'admin':
                // Admin gets most permissions except super admin specific ones
                $adminPermissions = $permissions->where('name', '!=', 'system.maintenance');
                $role->permissions()->sync($adminPermissions->pluck('id'));
                break;

            case 'manager':
                // Manager gets business management and module permissions
                $managerPermissions = $permissions->whereIn('module', [
                    'user_management',
                    'business_management',
                    'module_management',
                    'accounting',
                    'mix_design',
                    'security'
                ]);
                $role->permissions()->sync($managerPermissions->pluck('id'));
                break;

            case 'staff':
                // Staff gets basic permissions for assigned modules
                $staffPermissions = $permissions->whereIn('name', [
                    'users.view',
                    'business_types.view',
                    'modules.view',
                    'accounting.view',
                    'mix_designs.view'
                ]);
                $role->permissions()->sync($staffPermissions->pluck('id'));
                break;

            case 'auditor':
                // Auditor gets read-only permissions
                $auditorPermissions = $permissions->whereIn('name', [
                    'users.view',
                    'business_types.view',
                    'modules.view',
                    'accounting.view',
                    'mix_designs.view',
                    'security.events',
                    'security.audit'
                ]);
                $role->permissions()->sync($auditorPermissions->pluck('id'));
                break;

            case 'client':
                // Client gets very limited permissions
                $clientPermissions = $permissions->whereIn('name', [
                    'modules.view'
                ]);
                $role->permissions()->sync($clientPermissions->pluck('id'));
                break;
        }
    }
}
