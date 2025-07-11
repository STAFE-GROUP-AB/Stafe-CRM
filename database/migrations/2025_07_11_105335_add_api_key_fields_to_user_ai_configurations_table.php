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
        Schema::table('user_ai_configurations', function (Blueprint $table) {
            $table->string('api_key')->nullable()->after('ai_provider_id');
            $table->string('api_endpoint')->nullable()->after('api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ai_configurations', function (Blueprint $table) {
            $table->dropColumn(['api_key', 'api_endpoint']);
        });
    }
};
