<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_routing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('conditions'); // Rule conditions (field, operator, value)
            $table->json('assignment_rules'); // Assignment logic (round_robin, load_based, skill_based, etc.)
            $table->integer('priority')->default(0); // Rule priority (higher number = higher priority)
            $table->boolean('is_active')->default(true);
            $table->boolean('use_ai_scoring')->default(false); // Use AI for lead scoring in assignment
            $table->json('ai_parameters')->nullable(); // AI model parameters
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['tenant_id', 'is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_routing_rules');
    }
};