<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cadence_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['sales', 'marketing', 'onboarding', 'nurturing', 'follow_up']);
            $table->json('trigger_conditions'); // When to start this cadence
            $table->integer('total_steps')->default(0);
            $table->integer('duration_days')->nullable(); // Total sequence duration
            $table->boolean('is_active')->default(true);
            $table->json('exit_conditions')->nullable(); // When to exit the sequence
            $table->json('success_metrics')->nullable(); // Metrics to track success
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'is_active']);
            $table->index(['created_by_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadence_sequences');
    }
};