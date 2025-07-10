<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->foreignId('signer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('signer_name');
            $table->string('signer_email');
            $table->string('signer_title')->nullable();
            $table->text('signature_data'); // Base64 encoded signature image
            $table->string('ip_address');
            $table->string('user_agent');
            $table->enum('signature_type', ['draw', 'type', 'upload'])->default('draw');
            $table->timestamp('signed_at');
            $table->timestamps();

            $table->index(['quote_id', 'signed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_signatures');
    }
};