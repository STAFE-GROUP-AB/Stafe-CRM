<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('layout_config'); // Dashboard layout configuration
            $table->json('widgets'); // Widget configurations
            $table->boolean('is_default')->default(false);
            $table->boolean('is_public')->default(false);
            $table->enum('type', ['personal', 'team', 'company'])->default('personal');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['user_id', 'tenant_id']);
            $table->index(['type', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboards');
    }
};