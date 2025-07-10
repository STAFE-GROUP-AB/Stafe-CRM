<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event_type'); // login, logout, data_access, data_modification, etc.
            $table->string('event_category'); // authentication, data, system, security
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->morphs('auditable'); // What was acted upon
            $table->string('action'); // create, read, update, delete, login, etc.
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'event_type']);
            $table->index(['user_id', 'occurred_at']);
            $table->index(['event_category', 'risk_level']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_audit_logs');
    }
};