<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('battle_card_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battle_card_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('deal_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('viewed_at');
            $table->string('context')->nullable(); // deal_context, general_view, shared, etc.
            $table->boolean('was_shared')->default(false);
            $table->string('shared_via')->nullable(); // email, link, copy, etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['battle_card_id', 'viewed_at']);
            $table->index(['user_id', 'viewed_at']);
            $table->index(['deal_id', 'viewed_at']);
            $table->index(['context', 'viewed_at']);
        });
    }
};
