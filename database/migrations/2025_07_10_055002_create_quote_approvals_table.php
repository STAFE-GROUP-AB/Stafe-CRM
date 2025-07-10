<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comments')->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->decimal('discount_limit', 5, 2)->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['quote_id', 'status']);
            $table->index(['approver_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_approvals');
    }
};