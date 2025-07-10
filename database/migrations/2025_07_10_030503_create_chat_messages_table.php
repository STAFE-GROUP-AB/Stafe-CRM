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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->enum('sender_type', ['visitor', 'agent', 'bot'])->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // If sent by agent
            $table->text('message');
            $table->json('metadata')->nullable(); // Attachments, formatting, etc.
            
            // AI Analysis
            $table->json('ai_analysis')->nullable(); // Intent, sentiment, entities
            $table->decimal('sentiment_score', 3, 2)->nullable();
            $table->json('detected_intent')->nullable(); // What the user wants
            
            // Message status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['chat_session_id', 'created_at']);
            $table->index(['sender_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};