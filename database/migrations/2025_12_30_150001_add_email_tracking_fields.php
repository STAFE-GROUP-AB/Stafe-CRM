<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->string('tracking_token', 64)->nullable()->unique()->after('email_template_id');
            $table->integer('open_count')->default(0)->after('opened_at');
            $table->integer('click_count')->default(0)->after('clicked_at');
            $table->json('tracking_metadata')->nullable()->after('click_count');
        });
    }

    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn(['tracking_token', 'open_count', 'click_count', 'tracking_metadata']);
        });
    }
};
