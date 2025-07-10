<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('test_type', ['email_subject', 'email_content', 'cadence_sequence', 'template_variant', 'workflow_path']);
            $table->json('test_configuration'); // A/B test specific configuration
            $table->json('variant_a'); // Configuration for variant A
            $table->json('variant_b'); // Configuration for variant B
            $table->decimal('traffic_split', 3, 2)->default(0.50); // Percentage for variant A (0.00-1.00)
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'cancelled']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('minimum_sample_size')->default(100);
            $table->decimal('confidence_level', 3, 2)->default(0.95); // Statistical confidence level
            $table->json('success_metrics'); // Metrics to measure success
            $table->json('results')->nullable(); // Test results and statistics
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'test_type']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_tests');
    }
};