<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_triggers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('trigger_type', ['model_created', 'model_updated', 'model_deleted', 'field_changed', 'time_based', 'external_api', 'webhook', 'user_action']);
            $table->string('model_type')->nullable(); // Model class name for model-based triggers
            $table->json('trigger_conditions'); // Specific conditions for the trigger
            $table->json('action_configuration'); // What to do when triggered
            $table->integer('delay_minutes')->default(0); // Delay before executing action
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_retries')->default(true);
            $table->integer('max_retries')->default(3);
            $table->json('retry_configuration')->nullable(); // Retry logic configuration
            $table->json('rate_limiting')->nullable(); // Rate limiting rules
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['tenant_id', 'trigger_type', 'is_active']);
            $table->index(['model_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_triggers');
    }
};