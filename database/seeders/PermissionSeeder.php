<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // CRM Permissions
            ['name' => 'View Companies', 'slug' => 'companies.view', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Create Companies', 'slug' => 'companies.create', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Edit Companies', 'slug' => 'companies.edit', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Delete Companies', 'slug' => 'companies.delete', 'category' => 'crm', 'is_system' => true],
            
            ['name' => 'View Contacts', 'slug' => 'contacts.view', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Create Contacts', 'slug' => 'contacts.create', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Edit Contacts', 'slug' => 'contacts.edit', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Delete Contacts', 'slug' => 'contacts.delete', 'category' => 'crm', 'is_system' => true],
            
            ['name' => 'View Deals', 'slug' => 'deals.view', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Create Deals', 'slug' => 'deals.create', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Edit Deals', 'slug' => 'deals.edit', 'category' => 'crm', 'is_system' => true],
            ['name' => 'Delete Deals', 'slug' => 'deals.delete', 'category' => 'crm', 'is_system' => true],
            
            // Workflow Permissions
            ['name' => 'View Workflows', 'slug' => 'workflows.view', 'category' => 'automation', 'is_system' => true],
            ['name' => 'Create Workflows', 'slug' => 'workflows.create', 'category' => 'automation', 'is_system' => true],
            ['name' => 'Edit Workflows', 'slug' => 'workflows.edit', 'category' => 'automation', 'is_system' => true],
            ['name' => 'Delete Workflows', 'slug' => 'workflows.delete', 'category' => 'automation', 'is_system' => true],
            ['name' => 'Execute Workflows', 'slug' => 'workflows.execute', 'category' => 'automation', 'is_system' => true],
            
            // Integration Permissions
            ['name' => 'View Integrations', 'slug' => 'integrations.view', 'category' => 'integration', 'is_system' => true],
            ['name' => 'Install Integrations', 'slug' => 'integrations.install', 'category' => 'integration', 'is_system' => true],
            ['name' => 'Configure Integrations', 'slug' => 'integrations.configure', 'category' => 'integration', 'is_system' => true],
            ['name' => 'Manage API Connections', 'slug' => 'integrations.connections', 'category' => 'integration', 'is_system' => true],
            
            // Reporting Permissions
            ['name' => 'View Reports', 'slug' => 'reports.view', 'category' => 'reporting', 'is_system' => true],
            ['name' => 'Create Reports', 'slug' => 'reports.create', 'category' => 'reporting', 'is_system' => true],
            ['name' => 'Edit Reports', 'slug' => 'reports.edit', 'category' => 'reporting', 'is_system' => true],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'category' => 'reporting', 'is_system' => true],
            
            // Admin Permissions
            ['name' => 'Manage Users', 'slug' => 'admin.users', 'category' => 'admin', 'is_system' => true],
            ['name' => 'Manage Roles', 'slug' => 'admin.roles', 'category' => 'admin', 'is_system' => true],
            ['name' => 'Manage Permissions', 'slug' => 'admin.permissions', 'category' => 'admin', 'is_system' => true],
            ['name' => 'Manage Settings', 'slug' => 'admin.settings', 'category' => 'admin', 'is_system' => true],
            ['name' => 'View System Logs', 'slug' => 'admin.logs', 'category' => 'admin', 'is_system' => true],
            
            // Team Permissions
            ['name' => 'View Teams', 'slug' => 'teams.view', 'category' => 'team', 'is_system' => true],
            ['name' => 'Create Teams', 'slug' => 'teams.create', 'category' => 'team', 'is_system' => true],
            ['name' => 'Edit Teams', 'slug' => 'teams.edit', 'category' => 'team', 'is_system' => true],
            ['name' => 'Manage Team Members', 'slug' => 'teams.members', 'category' => 'team', 'is_system' => true],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}