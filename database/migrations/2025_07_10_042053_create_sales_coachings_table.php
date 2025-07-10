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
        Schema::create('sales_coachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('deal_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('coaching_type', [
                'deal_strategy', 'communication', 'negotiation', 'product_knowledge',
                'time_management', 'prospecting', 'objection_handling', 'closing'
            ]);
            $table->enum('priority_level', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->json('recommendations'); // AI-generated coaching recommendations
            $table->json('skill_gaps')->nullable(); // Identified skill gaps
            $table->json('performance_metrics')->nullable(); // Performance metrics analysis
            $table->json('action_items')->nullable(); // Specific action items
            $table->json('suggested_resources')->nullable(); // Training resources
            $table->decimal('coaching_score', 5, 2)->nullable(); // Overall coaching score
            $table->enum('implementation_status', [
                'not_started', 'pending', 'in_progress', 'completed', 'skipped'
            ])->default('pending');
            $table->text('coach_notes')->nullable(); // Human coach notes
            $table->date('follow_up_date')->nullable();
            $table->string('model_version')->nullable();
            $table->timestamp('last_generated_at');
            $table->foreignId('ai_model_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'coaching_type']);
            $table->index(['deal_id', 'priority_level']);
            $table->index('implementation_status');
            $table->index('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_coachings');
    }
};
