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
        Schema::create('territory_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('territory_name');
            $table->text('territory_description')->nullable();
            $table->decimal('performance_score', 5, 2)->default(0); // Overall performance score
            $table->decimal('revenue_target', 10, 2)->default(0);
            $table->decimal('revenue_actual', 10, 2)->default(0);
            $table->decimal('revenue_percentage', 5, 2)->default(0); // Percentage of target achieved
            $table->integer('deal_count_target')->default(0);
            $table->integer('deal_count_actual')->default(0);
            $table->decimal('activity_score', 5, 2)->default(0); // Activity performance score
            $table->json('optimization_recommendations')->nullable(); // Territory optimization suggestions
            $table->json('market_potential')->nullable(); // Market potential analysis
            $table->decimal('competition_density', 5, 4)->nullable(); // Competition density in territory
            $table->decimal('territory_balance_score', 5, 2)->nullable(); // Territory balance assessment
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('last_calculated_at');
            $table->timestamps();

            $table->index(['user_id', 'period_start', 'period_end']);
            $table->index('performance_score');
            $table->index('revenue_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('territory_performances');
    }
};
