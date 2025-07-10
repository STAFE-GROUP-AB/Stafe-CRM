<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leaderboard_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rank');
            $table->decimal('score', 12, 2);
            $table->json('metrics')->nullable(); // Detailed metrics breakdown
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->unique(['leaderboard_id', 'user_id', 'period_start'], 'leaderboard_user_period_unique');
            $table->index(['leaderboard_id', 'period_start', 'rank']);
            $table->index(['user_id', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_entries');
    }
};