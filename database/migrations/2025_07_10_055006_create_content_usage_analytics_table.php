<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_usage_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('sales_content')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('deal_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('action', ['view', 'download', 'share', 'rate', 'comment']);
            $table->json('metadata')->nullable(); // Additional action-specific data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('duration_seconds')->nullable(); // For view tracking
            $table->timestamps();

            $table->index(['content_id', 'action', 'created_at']);
            $table->index(['user_id', 'action', 'created_at']);
            $table->index(['deal_id', 'action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_usage_analytics');
    }
};