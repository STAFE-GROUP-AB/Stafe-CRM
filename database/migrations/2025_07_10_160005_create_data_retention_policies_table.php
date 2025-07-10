<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_retention_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->string('model_type'); // App\Models\Contact, App\Models\Deal, etc.
            $table->integer('retention_days'); // How long to keep the data
            $table->enum('action_after_retention', ['delete', 'anonymize', 'archive'])->default('archive');
            $table->json('conditions')->nullable(); // Additional conditions for the policy
            $table->string('date_field')->default('created_at'); // Which date field to use
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->integer('warning_days')->default(30); // Days before retention to warn
            $table->timestamp('last_executed_at')->nullable();
            $table->json('execution_results')->nullable(); // Results of last execution
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['model_type', 'is_active']);
            $table->index('last_executed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_retention_policies');
    }
};