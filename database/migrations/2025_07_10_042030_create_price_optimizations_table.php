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
        Schema::create('price_optimizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('suggested_price', 10, 2); // AI-suggested optimal price
            $table->decimal('confidence_score', 5, 4)->nullable(); // Model confidence (0.0000-1.0000)
            $table->json('price_factors')->nullable(); // Factors influencing pricing
            $table->json('market_analysis')->nullable(); // Market conditions analysis
            $table->json('competitor_pricing')->nullable(); // Competitor pricing data
            $table->json('historical_comparison')->nullable(); // Historical pricing patterns
            $table->json('discount_recommendations')->nullable(); // Discount strategies
            $table->string('pricing_strategy')->nullable(); // Recommended pricing strategy
            $table->json('margin_analysis')->nullable(); // Margin impact analysis
            $table->string('model_version')->nullable();
            $table->timestamp('last_calculated_at');
            $table->foreignId('ai_model_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['deal_id', 'last_calculated_at']);
            $table->index(['contact_id', 'company_id']);
            $table->index('confidence_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_optimizations');
    }
};
