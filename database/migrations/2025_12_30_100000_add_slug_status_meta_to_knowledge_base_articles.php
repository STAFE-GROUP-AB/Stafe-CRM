<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('is_published');
            $table->string('meta_description', 500)->nullable()->after('content');
        });

        // Remove the old is_published column as we now use status
        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });
    }
};
