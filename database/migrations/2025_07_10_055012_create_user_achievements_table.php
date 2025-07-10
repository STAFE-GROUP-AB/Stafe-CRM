<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            $table->json('criteria_met')->nullable(); // The specific criteria values that earned this achievement
            $table->integer('points_earned')->default(0);
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->unique(['user_id', 'achievement_id', 'earned_at'], 'user_achievement_unique');
            $table->index(['user_id', 'earned_at']);
            $table->index(['achievement_id', 'earned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};