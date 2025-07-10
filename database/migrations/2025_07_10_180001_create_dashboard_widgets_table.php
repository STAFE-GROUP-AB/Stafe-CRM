<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_id')->constrained()->onDelete('cascade');
            $table->string('widget_type'); // chart, metric, table, heatmap, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('configuration'); // Widget-specific configuration
            $table->json('data_source'); // Data source configuration
            $table->json('position'); // Position and size on dashboard
            $table->json('filters')->nullable(); // Widget-specific filters
            $table->integer('refresh_interval')->default(300); // Refresh in seconds
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['dashboard_id', 'widget_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};