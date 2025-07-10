<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_test_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ab_test_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->enum('variant', ['a', 'b']);
            $table->timestamp('enrolled_at');
            $table->json('tracking_data')->nullable(); // Data for tracking test results
            $table->boolean('converted')->default(false);
            $table->timestamp('conversion_at')->nullable();
            $table->json('conversion_data')->nullable(); // Additional conversion data
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['ab_test_id', 'variant']);
            $table->index(['contact_id', 'enrolled_at']);
            $table->index(['tenant_id', 'converted']);
            $table->unique(['ab_test_id', 'contact_id']); // One participant per test
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_test_participants');
    }
};