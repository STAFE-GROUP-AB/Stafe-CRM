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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('title')->nullable(); // Job title
            $table->string('department')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone')->default('UTC');
            $table->date('birthday')->nullable();
            $table->text('bio')->nullable();
            $table->json('social_links')->nullable(); // LinkedIn, Twitter, etc.
            $table->json('custom_fields')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('status')->default('active'); // active, inactive, lead, qualified, unqualified
            $table->string('source')->nullable(); // website, referral, social, etc.
            $table->decimal('lifetime_value', 15, 2)->default(0);
            $table->timestamp('last_contacted_at')->nullable();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['owner_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
