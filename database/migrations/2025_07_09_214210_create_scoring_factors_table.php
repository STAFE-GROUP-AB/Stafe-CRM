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
        Schema::create('scoring_factors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'email_engagement', 'company_size', 'industry_match', etc.
            $table->string('display_name'); // Human-readable name
            $table->text('description')->nullable();
            $table->string('category'); // 'demographic', 'behavioral', 'engagement', 'firmographic'
            $table->decimal('weight', 5, 4)->default(1.0000); // Factor weight in scoring
            $table->string('calculation_method'); // 'rule_based', 'ml_model', 'api_call'
            $table->json('configuration'); // Factor-specific configuration
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('data_source')->nullable(); // Where the data comes from
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_factors');
    }
};
