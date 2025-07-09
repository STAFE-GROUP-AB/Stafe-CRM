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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_provider_id')->constrained()->onDelete('cascade');
            $table->string('name'); // GPT-4, Claude-3, Gemini Pro, etc.
            $table->string('model_id'); // Provider's model identifier
            $table->text('description')->nullable();
            $table->json('capabilities'); // ['text_generation', 'code_generation', 'analysis', etc.]
            $table->json('pricing_info')->nullable(); // Cost per token, etc.
            $table->integer('max_tokens')->nullable();
            $table->integer('context_length')->nullable();
            $table->boolean('supports_streaming')->default(false);
            $table->boolean('supports_function_calling')->default(false);
            $table->string('status')->default('active'); // active, inactive, beta, deprecated
            $table->json('configuration_options')->nullable(); // Model-specific config options
            $table->timestamps();

            $table->index(['ai_provider_id', 'status']);
            $table->index('model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
