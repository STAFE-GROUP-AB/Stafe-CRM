<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_data_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->morphs('requestable'); // Subject of the request (Contact, Company, etc.)
            $table->enum('type', ['access', 'portability', 'rectification', 'erasure', 'restriction']);
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->text('description')->nullable();
            $table->string('requester_email');
            $table->string('requester_name')->nullable();
            $table->text('verification_details')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('deadline')->nullable(); // Legal deadline for response
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->text('processing_notes')->nullable();
            $table->json('exported_data')->nullable(); // For portability requests
            $table->timestamps();

            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_data_requests');
    }
};