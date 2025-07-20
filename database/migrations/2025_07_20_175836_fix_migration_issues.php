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
        // First, drop foreign key constraints that reference commission_trackings
        Schema::table('commission_trackings', function (Blueprint $table) {
            if (Schema::hasTable('commission_trackings')) {
                // Drop foreign key constraints
                $table->dropForeign(['user_id']);
                $table->dropForeign(['deal_id']);
                $table->dropForeign(['approved_by']);
                
                // Drop the table
                Schema::dropIfExists('commission_trackings');
            }
        });
        
        // Drop other tables that might have foreign key constraints
        $tables = [
            'deals',
            'pipeline_stages',
            'companies',
            'contacts',
            'users'
        ];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is intentionally left empty as we don't want to recreate the tables with data
    }
};
