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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('entity_type'); // contacts, companies, deals, tasks
            $table->json('filters'); // Search filters and criteria
            $table->json('columns')->nullable(); // Selected columns to display
            $table->string('sort_field')->nullable();
            $table->string('sort_direction')->default('asc');
            $table->boolean('is_global')->default(false); // Available to all users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['entity_type', 'is_global']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};