<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_visualizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('visualization_type', ['sankey', 'funnel', 'flow', 'timeline']);
            $table->json('pipeline_config'); // Pipeline configuration
            $table->json('visual_config'); // Visual styling configuration
            $table->json('data_points'); // Visualization data points
            $table->json('filters')->nullable(); // Applied filters
            $table->date('date_from');
            $table->date('date_to');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['visualization_type', 'tenant_id']);
            $table->index(['user_id', 'date_from', 'date_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_visualizations');
    }
};