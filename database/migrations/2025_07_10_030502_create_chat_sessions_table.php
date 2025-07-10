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
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique()->index();
            
            // Associated entities
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Assigned agent
            
            // Session details
            $table->enum('status', ['active', 'waiting', 'completed', 'abandoned'])->default('active')->index();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->string('visitor_phone')->nullable();
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            $table->json('visitor_info')->nullable(); // Browser, location, etc.
            
            // AI and Bot information
            $table->boolean('bot_active')->default(true);
            $table->foreignId('ai_model_id')->nullable()->constrained()->onDelete('set null');
            $table->json('bot_context')->nullable(); // Current conversation context for AI
            $table->boolean('qualified_lead')->nullable(); // AI qualification result
            $table->decimal('lead_score', 5, 2)->nullable();
            
            // Timestamps
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['contact_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};