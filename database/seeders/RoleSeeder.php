<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions',
                'is_system' => true,
                'permissions' => 'all', // All permissions
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access with most permissions',
                'is_system' => true,
                'permissions' => [
                    'companies.*', 'contacts.*', 'deals.*', 'reports.*',
                    'teams.*', 'workflows.view', 'workflows.create',
                    'integrations.view', 'integrations.install',
                ],
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Management level access',
                'is_system' => true,
                'permissions' => [
                    'companies.*', 'contacts.*', 'deals.*',
                    'reports.view', 'reports.create', 'reports.export',
                    'teams.view', 'teams.edit', 'teams.members',
                    'workflows.view', 'workflows.execute',
                ],
            ],
            [
                'name' => 'Sales Rep',
                'slug' => 'sales-rep',
                'description' => 'Sales representative access',
                'is_system' => true,
                'permissions' => [
                    'companies.view', 'companies.create', 'companies.edit',
                    'contacts.view', 'contacts.create', 'contacts.edit',
                    'deals.*', 'reports.view',
                ],
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Read-only access',
                'is_system' => true,
                'permissions' => [
                    'companies.view', 'contacts.view', 'deals.view',
                    'reports.view', 'teams.view',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'is_system' => $roleData['is_system'],
                ]
            );

            // Assign permissions
            if ($roleData['permissions'] === 'all') {
                // Assign all permissions to super admin
                $permissions = Permission::all();
                $role->permissions()->sync($permissions->pluck('id'));
            } else {
                // Assign specific permissions
                $permissionIds = [];
                foreach ($roleData['permissions'] as $permissionPattern) {
                    if (str_ends_with($permissionPattern, '.*')) {
                        // Match permissions by category or prefix
                        $prefix = rtrim($permissionPattern, '.*');
                        $permissions = Permission::where('slug', 'like', $prefix . '.%')->get();
                        $permissionIds = array_merge($permissionIds, $permissions->pluck('id')->toArray());
                    } else {
                        // Exact permission match
                        $permission = Permission::where('slug', $permissionPattern)->first();
                        if ($permission) {
                            $permissionIds[] = $permission->id;
                        }
                    }
                }
                $role->permissions()->sync(array_unique($permissionIds));
            }
        }
    }
}