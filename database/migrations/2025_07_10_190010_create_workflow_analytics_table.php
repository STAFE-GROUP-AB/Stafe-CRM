<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('workflow_type'); // Type of workflow being tracked
            $table->string('workflow_id'); // ID of the specific workflow instance
            $table->string('metric_name'); // Name of the metric being tracked
            $table->decimal('metric_value', 10, 4); // Value of the metric
            $table->json('metric_metadata')->nullable(); // Additional metric data
            $table->date('metric_date'); // Date this metric applies to
            $table->string('aggregation_period'); // daily, weekly, monthly
            $table->json('dimensions')->nullable(); // Additional dimensions (user, team, etc.)
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['workflow_type', 'workflow_id', 'metric_date']);
            $table->index(['tenant_id', 'metric_name', 'metric_date']);
            $table->index(['aggregation_period', 'metric_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_analytics');
    }
};