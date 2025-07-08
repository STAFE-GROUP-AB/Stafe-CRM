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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('industry')->nullable();
            $table->integer('employee_count')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone')->default('UTC');
            $table->text('description')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('status')->default('active'); // active, inactive, archived
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['owner_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
