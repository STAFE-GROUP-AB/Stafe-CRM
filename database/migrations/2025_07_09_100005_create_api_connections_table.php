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
        Schema::create('api_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // user-friendly name for this connection
            $table->json('config'); // connection-specific configuration
            $table->json('credentials'); // encrypted credentials
            $table->string('status'); // active, inactive, error
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_error')->nullable();
            $table->json('sync_stats')->nullable(); // sync statistics
            $table->timestamps();
            
            $table->index(['integration_id', 'user_id']);
            $table->index(['status', 'last_sync_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_connections');
    }
};