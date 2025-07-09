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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // OpenAI, Anthropic, Google, etc.
            $table->string('slug')->unique(); // openai, anthropic, google
            $table->text('description')->nullable();
            $table->string('api_base_url');
            $table->json('supported_features'); // ['text_generation', 'embeddings', 'image_analysis', etc.]
            $table->json('authentication_methods'); // ['api_key', 'oauth', 'bearer_token']
            $table->json('configuration_schema'); // JSON schema for provider configuration
            $table->string('status')->default('active'); // active, inactive, deprecated
            $table->string('logo_url')->nullable();
            $table->json('rate_limits')->nullable(); // Provider-specific rate limits
            $table->timestamps();

            $table->index('status');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
