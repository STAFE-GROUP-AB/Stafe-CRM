<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->json('responses'); // Store all question responses
            $table->decimal('nps_score', 3, 1)->nullable(); // For NPS surveys
            $table->decimal('csat_score', 3, 1)->nullable(); // For CSAT surveys
            $table->decimal('ces_score', 3, 1)->nullable(); // For CES surveys
            $table->text('feedback')->nullable(); // Open feedback
            $table->boolean('is_completed')->default(false);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};