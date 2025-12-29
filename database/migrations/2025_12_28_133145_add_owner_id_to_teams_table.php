<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('id');
        });

        // Copy user_id to owner_id for existing records
        DB::table('teams')->update(['owner_id' => DB::raw('user_id')]);

        // Make owner_id not nullable after data migration
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            //
        });
    }
};
