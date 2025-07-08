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
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#6B7280'); // Hex color for UI
            $table->integer('order')->default(0);
            $table->integer('default_probability')->default(0); // 0-100
            $table->boolean('is_active')->default(true);
            $table->boolean('is_closed')->default(false); // Won or lost stage
            $table->boolean('is_won')->default(false); // Won stage
            $table->json('settings')->nullable(); // Additional stage settings
            $table->timestamps();
            
            $table->index(['is_active', 'order']);
            $table->index(['is_closed', 'is_won']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};
