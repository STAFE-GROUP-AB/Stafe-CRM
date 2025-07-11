<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->morphs('consentable'); // Can belong to Contact, Company, etc.
            $table->string('purpose'); // marketing, analytics, essential, etc.
            $table->enum('status', ['granted', 'denied', 'withdrawn', 'expired'])->default('denied');
            $table->text('description')->nullable();
            $table->string('legal_basis'); // consent, legitimate_interest, contract, etc.
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // $table->index(['consentable_type', 'consentable_id']); // Already created by morphs()
            $table->index(['tenant_id', 'status']);
            $table->index('purpose');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_consents');
    }
};