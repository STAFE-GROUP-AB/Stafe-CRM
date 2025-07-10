<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['points', 'deals_closed', 'revenue_generated', 'calls_made', 'emails_sent', 'meetings_scheduled', 'custom'])->default('points');
            $table->enum('period', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'all_time'])->default('monthly');
            $table->json('filters')->nullable(); // Additional filters (team, role, etc.)
            $table->json('calculation_rules')->nullable(); // How to calculate the metric
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true); // Whether visible to all users
            $table->integer('display_limit')->default(10); // Number of top performers to show
            $table->integer('sort_order')->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'period', 'is_active']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};