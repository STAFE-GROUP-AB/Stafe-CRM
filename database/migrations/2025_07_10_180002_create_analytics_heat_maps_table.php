<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_heat_maps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['sales_activity', 'performance', 'geographic', 'time_based']);
            $table->json('configuration'); // Heat map configuration
            $table->json('data_points'); // Heat map data points
            $table->json('color_scheme'); // Color scheme configuration
            $table->date('date_from');
            $table->date('date_to');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['type', 'tenant_id']);
            $table->index(['user_id', 'date_from', 'date_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_heat_maps');
    }
};