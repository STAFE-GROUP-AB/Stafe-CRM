<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('domain')->unique()->nullable(); // custom domain
            $table->string('subdomain')->unique(); // tenant subdomain
            $table->json('settings')->nullable(); // tenant-specific settings
            $table->string('status'); // active, inactive, suspended
            $table->integer('max_users')->default(10); // user limit
            $table->integer('storage_limit')->default(1024); // storage limit in MB
            $table->json('features')->nullable(); // enabled features
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['subdomain']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};