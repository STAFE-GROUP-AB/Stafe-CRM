<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Assigned user
            $table->foreignId('lead_routing_rule_id')->nullable()->constrained()->onDelete('set null');
            $table->json('assignment_reason'); // Why this assignment was made
            $table->decimal('ai_confidence_score', 5, 4)->nullable(); // AI confidence in assignment
            $table->enum('assignment_method', ['manual', 'rule_based', 'ai_powered', 'round_robin']);
            $table->timestamp('assigned_at');
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['user_id', 'assigned_at']);
            $table->index(['tenant_id', 'assignment_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_assignments');
    }
};