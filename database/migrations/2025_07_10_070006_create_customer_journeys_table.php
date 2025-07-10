<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_journeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('current_stage_id')->constrained('customer_journey_stages')->onDelete('cascade');
            $table->timestamp('stage_entered_at');
            $table->json('stage_history'); // Track progression through stages
            $table->json('touchpoints')->nullable(); // Record of all interactions
            $table->decimal('progression_score', 5, 2)->nullable(); // How well they're progressing
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_journeys');
    }
};