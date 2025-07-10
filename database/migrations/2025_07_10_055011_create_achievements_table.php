<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('badge_image')->nullable();
            $table->enum('category', ['sales', 'activity', 'social', 'learning', 'milestone'])->default('sales');
            $table->enum('type', ['numeric', 'boolean', 'streak', 'percentage'])->default('numeric');
            $table->json('criteria'); // Achievement criteria (e.g., {'deals_closed': 10, 'period': 'month'})
            $table->integer('points')->default(0); // Points awarded for this achievement
            $table->enum('rarity', ['common', 'uncommon', 'rare', 'epic', 'legendary'])->default('common');
            $table->boolean('is_repeatable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['type', 'is_active']);
            $table->index(['rarity', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};