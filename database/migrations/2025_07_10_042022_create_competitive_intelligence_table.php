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
        Schema::create('competitive_intelligence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->string('competitor_name');
            $table->json('competitor_strength')->nullable(); // Array of competitor strengths
            $table->json('competitor_weaknesses')->nullable(); // Array of competitor weaknesses
            $table->decimal('win_loss_probability', 5, 4)->nullable(); // Probability of winning vs this competitor
            $table->json('competitive_factors')->nullable(); // Factors affecting competition
            $table->json('battle_card_recommendations')->nullable(); // Recommended strategies
            $table->json('pricing_comparison')->nullable(); // Pricing comparison data
            $table->json('feature_comparison')->nullable(); // Feature comparison data
            $table->string('source')->nullable(); // Source of intelligence (manual, automated, etc.)
            $table->decimal('confidence_score', 5, 4)->nullable(); // Confidence in the analysis
            $table->timestamp('last_updated_at');
            $table->timestamps();

            $table->index(['deal_id', 'competitor_name']);
            $table->index('competitor_name');
            $table->index('win_loss_probability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitive_intelligence');
    }
};
