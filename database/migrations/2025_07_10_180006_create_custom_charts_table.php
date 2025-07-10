<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_charts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('chart_type', ['line', 'bar', 'pie', 'donut', 'scatter', 'bubble', 'area', 'radar', 'heatmap', 'treemap', 'gauge']);
            $table->json('data_source'); // Data source configuration
            $table->json('chart_config'); // Chart configuration
            $table->json('styling'); // Chart styling configuration
            $table->json('filters')->nullable(); // Chart filters
            $table->json('drill_down_config')->nullable(); // Drill down configuration
            $table->boolean('is_real_time')->default(false);
            $table->integer('refresh_interval')->default(300); // Refresh in seconds
            $table->boolean('is_public')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['chart_type', 'tenant_id']);
            $table->index(['user_id', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_charts');
    }
};