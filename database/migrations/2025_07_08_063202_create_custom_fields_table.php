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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('type'); // text, textarea, number, date, select, checkbox, etc.
            $table->string('entity_type'); // Company, Contact, Deal, etc.
            $table->text('description')->nullable();
            $table->json('options')->nullable(); // For select fields
            $table->string('default_value')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->json('validation_rules')->nullable();
            $table->timestamps();
            
            $table->index(['entity_type', 'is_active', 'order']);
            $table->unique(['entity_type', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
