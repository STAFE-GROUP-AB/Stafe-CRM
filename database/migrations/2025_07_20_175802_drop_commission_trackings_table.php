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
        Schema::dropIfExists('commission_trackings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is intentionally left empty as we don't want to recreate the table with data
    }
};
