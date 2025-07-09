<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add tenant_id to existing tables for multi-tenancy
        $tables = [
            'companies',
            'contacts', 
            'deals',
            'tasks',
            'notes',
            'custom_fields',
            'email_templates',
            'emails',
            'reports',
            'activity_logs',
            'import_jobs',
            'saved_searches',
            'teams',
            'comments',
            'notifications',
            'workflow_templates',
            'workflow_instances',
            'api_connections'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
                    $blueprint->index(['tenant_id']);
                });
            }
        }

        // Add tenant_id to users table for user-tenant relationships
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->index(['current_tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'companies',
            'contacts', 
            'deals',
            'tasks',
            'notes',
            'custom_fields',
            'email_templates',
            'emails',
            'reports',
            'activity_logs',
            'import_jobs',
            'saved_searches',
            'teams',
            'comments',
            'notifications',
            'workflow_templates',
            'workflow_instances',
            'api_connections'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropForeign(['tenant_id']);
                    $blueprint->dropColumn('tenant_id');
                });
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_tenant_id']);
            $table->dropColumn('current_tenant_id');
        });
    }
};