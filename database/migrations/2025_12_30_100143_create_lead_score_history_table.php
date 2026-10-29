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
        Schema::create('lead_score_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_score_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->integer('previous_score')->nullable();
            $table->integer('new_score');
            $table->string('change_reason');
            $table->json('factors_snapshot')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['contact_id', 'created_at']);
            $table->index('lead_score_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_score_history');
    }
};
