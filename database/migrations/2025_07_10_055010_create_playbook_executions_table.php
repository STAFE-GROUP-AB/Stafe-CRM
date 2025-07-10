<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playbook_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playbook_id')->constrained('sales_playbooks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('deal_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->json('step_results')->nullable(); // Results for each completed step
            $table->integer('current_step_order')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->enum('outcome', ['successful', 'unsuccessful', 'partial'])->nullable();
            $table->text('notes')->nullable();
            $table->integer('rating')->nullable(); // 1-5 rating of the playbook effectiveness
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index(['playbook_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['deal_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playbook_executions');
    }
};