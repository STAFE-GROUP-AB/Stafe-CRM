<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('set null');
            $table->text('message');
            $table->enum('response_type', ['agent', 'customer', 'system'])->default('agent');
            $table->boolean('is_internal_note')->default(false);
            $table->integer('response_time_minutes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['customer_ticket_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }
};
