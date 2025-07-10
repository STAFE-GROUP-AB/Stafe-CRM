<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_trigger_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_trigger_id')->constrained()->onDelete('cascade');
            $table->json('trigger_data'); // Data that triggered the event
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'retrying']);
            $table->timestamp('triggered_at');
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('execution_result')->nullable(); // Result of the action
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->json('execution_context')->nullable(); // Additional context data
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'next_retry_at']);
            $table->index(['event_trigger_id', 'triggered_at']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_trigger_executions');
    }
};