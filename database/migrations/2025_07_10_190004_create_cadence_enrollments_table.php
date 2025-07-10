<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cadence_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cadence_sequence_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrolled_by_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'paused', 'completed', 'exited', 'failed']);
            $table->integer('current_step')->default(0);
            $table->timestamp('enrolled_at');
            $table->timestamp('next_action_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('enrollment_data')->nullable(); // Custom data for this enrollment
            $table->json('step_history')->nullable(); // History of completed steps
            $table->text('exit_reason')->nullable();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'next_action_at']);
            $table->index(['contact_id', 'cadence_sequence_id']);
            $table->index(['tenant_id', 'enrolled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadence_enrollments');
    }
};