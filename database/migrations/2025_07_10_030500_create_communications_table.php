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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['call', 'sms', 'email', 'video', 'whatsapp', 'social', 'chat'])->index();
            $table->enum('direction', ['inbound', 'outbound'])->index();
            $table->enum('status', ['initiated', 'ringing', 'answered', 'completed', 'failed', 'busy', 'no-answer'])->default('initiated')->index();
            
            // Polymorphic relationship to any entity (contact, company, deal, etc.)
            $table->morphs('communicable');
            
            // User who initiated/handled the communication
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Content and metadata
            $table->text('content')->nullable();
            $table->json('metadata')->nullable(); // Store provider-specific data, participants, etc.
            
            // Call/Video specific fields
            $table->integer('duration_seconds')->nullable();
            $table->string('recording_url')->nullable();
            $table->text('transcript')->nullable();
            $table->decimal('sentiment_score', 3, 2)->nullable(); // -1 to 1
            
            // External provider information
            $table->string('external_id')->nullable(); // Twilio SID, etc.
            $table->string('provider')->nullable(); // twilio, zoom, etc.
            $table->json('provider_data')->nullable();
            
            // Contact information
            $table->string('from_number')->nullable();
            $table->string('to_number')->nullable();
            $table->string('from_email')->nullable();
            $table->string('to_email')->nullable();
            
            // AI Analysis
            $table->json('ai_analysis')->nullable(); // Keywords, topics, intent, etc.
            $table->json('follow_up_suggestions')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};