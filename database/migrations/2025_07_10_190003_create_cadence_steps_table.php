<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cadence_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cadence_sequence_id')->constrained()->onDelete('cascade');
            $table->integer('step_number'); // Order in sequence
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('action_type', ['email', 'call', 'task', 'sms', 'social', 'wait', 'condition_check']);
            $table->json('action_config'); // Action-specific configuration
            $table->integer('delay_days')->default(0); // Days to wait before this step
            $table->integer('delay_hours')->default(0); // Additional hours to wait
            $table->json('conditions')->nullable(); // Conditions to execute this step
            $table->json('personalization_rules')->nullable(); // Dynamic content rules
            $table->boolean('is_active')->default(true);
            $table->boolean('allows_manual_skip')->default(true);
            $table->timestamps();

            $table->index(['cadence_sequence_id', 'step_number']);
            $table->index(['action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadence_steps');
    }
};