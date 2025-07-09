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
        Schema::create('lead_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0); // 0-100 lead score
            $table->decimal('probability', 5, 4)->nullable(); // Conversion probability (0.0000-1.0000)
            $table->string('grade')->nullable(); // A, B, C, D, F
            $table->json('factors'); // Array of scoring factors and their contributions
            $table->json('explanations')->nullable(); // Human-readable explanations for the score
            $table->string('model_version')->nullable(); // Version of the scoring model used
            $table->timestamp('last_calculated_at');
            $table->foreignId('ai_model_id')->nullable()->constrained()->onDelete('set null');
            $table->json('raw_predictions')->nullable(); // Raw ML model output
            $table->timestamps();

            $table->index(['contact_id', 'last_calculated_at']);
            $table->index('score');
            $table->index('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_scores');
    }
};
