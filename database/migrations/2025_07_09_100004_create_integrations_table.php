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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('integration_categories');
            $table->string('provider'); // e.g., 'zapier', 'make', 'custom'
            $table->string('version')->default('1.0.0');
            $table->json('config_schema'); // JSON schema for configuration
            $table->json('webhook_endpoints')->nullable(); // available webhook endpoints
            $table->json('api_endpoints')->nullable(); // available API endpoints
            $table->string('auth_type'); // oauth, api_key, basic, none
            $table->json('auth_config')->nullable(); // auth configuration
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('install_count')->default(0);
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index(['provider', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};