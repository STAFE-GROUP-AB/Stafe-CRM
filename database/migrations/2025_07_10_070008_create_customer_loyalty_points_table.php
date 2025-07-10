<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('loyalty_program_id')->constrained()->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->integer('available_points')->default(0); // Points available for redemption
            $table->integer('redeemed_points')->default(0);
            $table->string('current_tier')->nullable();
            $table->datetime('tier_achieved_at')->nullable();
            $table->json('tier_benefits')->nullable(); // Current tier benefits
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_loyalty_points');
    }
};