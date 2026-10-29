<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_tickets', function (Blueprint $table) {
            $table->timestamp('first_response_at')->nullable()->after('resolved_at');
            $table->json('response_times')->nullable()->after('first_response_at');
            $table->boolean('first_response_sla_breached')->default(false)->after('response_times');
            $table->boolean('resolution_sla_breached')->default(false)->after('first_response_sla_breached');
            $table->integer('total_response_time_minutes')->nullable()->after('resolution_sla_breached');
            $table->integer('total_resolution_time_minutes')->nullable()->after('total_response_time_minutes');
            $table->timestamp('sla_due_at')->nullable()->after('total_resolution_time_minutes');
        });
    }
};
