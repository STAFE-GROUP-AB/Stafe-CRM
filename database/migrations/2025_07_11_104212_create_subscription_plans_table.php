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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Price in SEK
            $table->string('currency', 3)->default('SEK');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('yearly');
            $table->integer('max_users')->default(1);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_trial')->default(false);
            $table->integer('trial_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
