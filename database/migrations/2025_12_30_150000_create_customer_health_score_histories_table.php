<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_health_score_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->decimal('overall_score', 5, 2);
            $table->json('score_breakdown');
            $table->enum('health_status', ['excellent', 'good', 'at_risk', 'critical']);
            $table->decimal('previous_score', 5, 2)->nullable();
            $table->enum('previous_status', ['excellent', 'good', 'at_risk', 'critical'])->nullable();
            $table->decimal('score_change', 5, 2)->nullable();
            $table->string('trigger')->default('scheduled');
            $table->timestamps();

            $table->index(['contact_id', 'created_at']);
            $table->index('created_at');
        });

        Schema::table('customer_health_scores', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_health_scores', 'previous_score')) {
                $table->decimal('previous_score', 5, 2)->nullable()->after('overall_score');
            }
            if (!Schema::hasColumn('customer_health_scores', 'trend')) {
                $table->enum('trend', ['improving', 'stable', 'declining'])->default('stable')->after('health_status');
            }
            if (!Schema::hasColumn('customer_health_scores', 'trend_percentage')) {
                $table->decimal('trend_percentage', 5, 2)->default(0)->after('trend');
            }
            if (!Schema::hasColumn('customer_health_scores', 'alert_sent_at')) {
                $table->datetime('alert_sent_at')->nullable()->after('last_calculated_at');
            }
        });
    }
};
