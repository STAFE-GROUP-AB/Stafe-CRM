<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playbook_step_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('execution_id')->constrained('playbook_executions')->onDelete('cascade');
            $table->foreignId('step_id')->constrained('playbook_steps')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->text('notes')->nullable();
            $table->text('skipped_reason')->nullable();
            $table->json('result_data')->nullable();
            $table->timestamps();

            $table->index(['execution_id', 'step_id']);
            $table->index(['execution_id', 'status']);
            $table->index(['step_id', 'status']);
            $table->unique(['execution_id', 'step_id']);
        });
    }
};
