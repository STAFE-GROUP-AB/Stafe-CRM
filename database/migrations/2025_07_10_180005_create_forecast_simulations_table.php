<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecast_simulations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('base_scenario'); // Base scenario configuration
            $table->json('scenarios'); // What-if scenarios
            $table->json('variables'); // Input variables for simulation
            $table->json('results'); // Simulation results
            $table->json('assumptions'); // Simulation assumptions
            $table->enum('simulation_type', ['revenue', 'pipeline', 'performance', 'market']);
            $table->date('forecast_start_date');
            $table->date('forecast_end_date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['simulation_type', 'tenant_id']);
            $table->index(['user_id', 'forecast_start_date', 'forecast_end_date'], 'idx_forecast_sim_user_dates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_simulations');
    }
};