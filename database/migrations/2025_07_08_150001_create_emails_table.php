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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique()->nullable();
            $table->string('direction'); // inbound, outbound
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->json('to_recipients');
            $table->json('cc_recipients')->nullable();
            $table->json('bcc_recipients')->nullable();
            $table->string('subject');
            $table->text('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default('pending'); // pending, sent, delivered, failed, bounced
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->string('error_message')->nullable();
            
            // Polymorphic relationship to link emails to any entity
            $table->morphs('emailable');
            
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('email_template_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            
            $table->index(['direction', 'status']);
            $table->index(['emailable_type', 'emailable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};