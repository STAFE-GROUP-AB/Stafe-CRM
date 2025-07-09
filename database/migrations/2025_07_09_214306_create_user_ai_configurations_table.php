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
        Schema::create('user_ai_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ai_provider_id')->constrained()->onDelete('cascade');
            $table->string('name'); // User-friendly name for this configuration
            $table->json('credentials'); // Encrypted API keys and credentials
            $table->json('default_models'); // Default model IDs for different use cases
            $table->json('settings')->nullable(); // User-specific settings and preferences
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Default configuration for user
            $table->timestamp('last_used_at')->nullable();
            $table->json('usage_stats')->nullable(); // Usage tracking
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'is_default']);
            $table->unique(['user_id', 'ai_provider_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ai_configurations');
    }
};
