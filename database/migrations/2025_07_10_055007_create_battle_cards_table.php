<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('battle_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('competitor_name');
            $table->string('competitor_logo')->nullable();
            $table->text('overview')->nullable();
            $table->json('our_strengths')->nullable(); // Array of our strengths vs this competitor
            $table->json('our_weaknesses')->nullable(); // Array of our weaknesses vs this competitor
            $table->json('competitor_strengths')->nullable(); // Array of competitor strengths
            $table->json('competitor_weaknesses')->nullable(); // Array of competitor weaknesses
            $table->json('key_differentiators')->nullable(); // Key points that differentiate us
            $table->json('pricing_comparison')->nullable(); // Pricing comparison data
            $table->json('feature_comparison')->nullable(); // Feature comparison matrix
            $table->json('objection_handling')->nullable(); // Common objections and responses
            $table->json('winning_strategies')->nullable(); // Strategies to win against this competitor
            $table->json('recent_wins')->nullable(); // Recent wins against this competitor
            $table->json('recent_losses')->nullable(); // Recent losses and lessons learned
            $table->decimal('win_rate', 5, 2)->default(0); // Win rate percentage against this competitor
            $table->text('sales_notes')->nullable(); // Additional sales notes
            $table->json('resources')->nullable(); // Links to additional resources
            $table->enum('threat_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_updated_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('usage_count')->default(0); // How many times used in deals
            $table->timestamps();

            $table->index(['competitor_name', 'status']);
            $table->index(['threat_level', 'status']);
            $table->index(['created_by', 'status']);
            $table->fullText(['title', 'competitor_name', 'overview']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battle_cards');
    }
};