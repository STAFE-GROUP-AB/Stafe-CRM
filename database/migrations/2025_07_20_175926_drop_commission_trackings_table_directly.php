<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to drop the table if it exists
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('DROP TABLE IF EXISTS commission_trackings;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is intentionally left empty as we don't want to recreate the table with data
    }
};
