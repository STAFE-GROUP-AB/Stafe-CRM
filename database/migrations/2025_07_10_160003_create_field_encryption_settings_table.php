<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_encryption_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('model_type'); // App\Models\Contact, App\Models\Company, etc.
            $table->string('field_name'); // email, phone, ssn, etc.
            $table->boolean('is_encrypted')->default(false);
            $table->string('encryption_algorithm')->default('AES-256-GCM');
            $table->enum('sensitivity_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'model_type', 'field_name']);
            $table->index(['model_type', 'is_encrypted']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_encryption_settings');
    }
};