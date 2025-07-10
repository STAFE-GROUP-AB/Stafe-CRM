<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playbook_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playbook_id')->constrained('sales_playbooks')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions'); // Detailed step instructions
            $table->enum('step_type', ['action', 'question', 'script', 'decision', 'checklist'])->default('action');
            $table->json('content')->nullable(); // Step-specific content (scripts, questions, etc.)
            $table->json('resources')->nullable(); // Links to supporting materials
            $table->text('success_criteria')->nullable(); // How to know this step was successful
            $table->text('failure_handling')->nullable(); // What to do if step fails
            $table->integer('sort_order')->default(0);
            $table->integer('estimated_duration_minutes')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['playbook_id', 'sort_order']);
            $table->index(['step_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playbook_steps');
    }
};