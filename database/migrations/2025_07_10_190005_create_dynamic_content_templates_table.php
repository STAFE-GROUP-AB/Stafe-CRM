<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_content_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('content_type', ['email_subject', 'email_body', 'sms_message', 'task_description', 'social_post']);
            $table->text('base_template'); // Base template with placeholders
            $table->json('personalization_rules'); // Rules for dynamic content
            $table->json('variable_mappings'); // Map variables to data sources
            $table->json('conditional_content')->nullable(); // If-then content rules
            $table->boolean('is_active')->default(true);
            $table->json('usage_statistics')->nullable(); // Performance tracking
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['tenant_id', 'content_type', 'is_active']);
            $table->index(['created_by_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_content_templates');
    }
};