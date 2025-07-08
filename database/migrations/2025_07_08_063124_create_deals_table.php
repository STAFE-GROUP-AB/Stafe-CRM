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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('value', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('probability')->default(0); // 0-100
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->string('status')->default('open'); // open, won, lost, archived
            $table->foreignId('pipeline_stage_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('source')->nullable(); // website, referral, cold call, etc.
            $table->string('type')->nullable(); // new business, existing business, renewal
            $table->text('close_reason')->nullable(); // Why won/lost
            $table->json('custom_fields')->nullable();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['owner_id', 'status']);
            $table->index(['pipeline_stage_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index(['contact_id', 'status']);
            $table->index(['expected_close_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
