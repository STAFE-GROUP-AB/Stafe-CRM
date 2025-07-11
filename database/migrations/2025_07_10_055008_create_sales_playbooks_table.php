<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_playbooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['discovery', 'demo', 'objection_handling', 'closing', 'follow_up', 'onboarding'])->default('discovery');
            $table->json('target_personas')->nullable(); // Target customer personas
            $table->json('deal_stages')->nullable(); // Applicable deal stages
            $table->text('overview')->nullable();
            $table->json('objectives')->nullable(); // Learning objectives
            $table->json('prerequisites')->nullable(); // What to know before using this playbook
            $table->text('estimated_duration')->nullable(); // Estimated time to complete
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('usage_count')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0); // Success rate when used
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['difficulty_level', 'status']);
            $table->index(['created_by', 'status']);
            // $table->fullText(['title', 'description', 'overview']); // Commented out for SQLite compatibility
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_playbooks');
    }
};