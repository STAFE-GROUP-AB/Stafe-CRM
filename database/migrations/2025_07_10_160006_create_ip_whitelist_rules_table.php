<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_whitelist_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->string('ip_address'); // Single IP or CIDR notation
            $table->text('description')->nullable();
            $table->enum('rule_type', ['allow', 'deny'])->default('allow');
            $table->json('allowed_actions')->nullable(); // Specific actions this IP can perform
            $table->json('restricted_paths')->nullable(); // Paths this rule applies to
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('priority')->default(0); // Higher priority rules are evaluated first
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'is_active']);
            $table->index(['rule_type', 'is_active']);
            $table->index('priority');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_whitelist_rules');
    }
};