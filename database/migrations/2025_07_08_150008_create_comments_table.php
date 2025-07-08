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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->boolean('is_internal')->default(false); // Internal team comment vs customer-facing
            $table->json('mentions')->nullable(); // User IDs mentioned in the comment
            
            // Polymorphic relationship to add comments to any entity
            $table->morphs('commentable');
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade'); // For threaded comments
            $table->timestamps();
            
            $table->index(['commentable_type', 'commentable_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};