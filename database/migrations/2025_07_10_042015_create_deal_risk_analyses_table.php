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
        Schema::create('deal_risk_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->integer('risk_score')->default(0); // 0-100 risk score
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->json('risk_factors'); // Array of risk factors and their contributions
            $table->json('intervention_recommendations')->nullable(); // Recommended actions
            $table->decimal('probability_to_close', 5, 4)->nullable(); // Probability of closing (0.0000-1.0000)
            $table->date('predicted_close_date')->nullable();
            $table->decimal('confidence_score', 5, 4)->nullable(); // Model confidence (0.0000-1.0000)
            $table->string('model_version')->nullable();
            $table->timestamp('last_analyzed_at');
            $table->foreignId('ai_model_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['deal_id', 'last_analyzed_at']);
            $table->index('risk_level');
            $table->index('risk_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_risk_analyses');
    }
};
