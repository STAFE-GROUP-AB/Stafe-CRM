<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relationship_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('nodes'); // Network nodes (contacts, companies, etc.)
            $table->json('edges'); // Network connections/relationships
            $table->json('layout_config'); // Network layout configuration
            $table->json('visual_config'); // Visual styling configuration
            $table->enum('network_type', ['contact', 'company', 'deal', 'mixed']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();

            $table->index(['network_type', 'tenant_id']);
            $table->index(['user_id', 'last_updated']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relationship_networks');
    }
};