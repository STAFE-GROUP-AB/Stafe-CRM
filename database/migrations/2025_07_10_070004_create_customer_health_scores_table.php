<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_health_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->decimal('overall_score', 5, 2); // 0-100 health score
            $table->json('score_breakdown'); // Individual factor scores
            $table->enum('health_status', ['excellent', 'good', 'at_risk', 'critical'])->default('good');
            $table->json('risk_factors')->nullable(); // Array of identified risks
            $table->json('improvement_suggestions')->nullable(); // AI-generated suggestions
            $table->datetime('last_calculated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_health_scores');
    }
};