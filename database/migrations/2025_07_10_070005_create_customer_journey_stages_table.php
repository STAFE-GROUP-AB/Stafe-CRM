<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_journey_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order_index');
            $table->string('color')->default('#3B82F6'); // Hex color for visualization
            $table->json('expected_actions')->nullable(); // Expected customer actions
            $table->json('success_criteria')->nullable(); // Criteria to move to next stage
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_journey_stages');
    }
};