<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sso_providers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name'); // Google, Microsoft, Okta, etc.
            $table->string('provider_type'); // saml, oauth, oidc
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable(); // Encrypted
            $table->text('certificate')->nullable(); // For SAML
            $table->string('entity_id')->nullable(); // For SAML
            $table->string('sso_url')->nullable(); // Login URL
            $table->string('sls_url')->nullable(); // Single Logout URL
            $table->json('attribute_mapping')->nullable(); // Map provider attributes to user fields
            $table->json('scopes')->nullable(); // OAuth scopes
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_provision')->default(false); // Auto-create users
            $table->string('default_role')->nullable(); // Default role for auto-provisioned users
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index('provider_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sso_providers');
    }
};