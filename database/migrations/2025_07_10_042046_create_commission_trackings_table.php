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
        Schema::create('commission_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 4)->nullable(); // Commission rate (0.0000-1.0000)
            $table->enum('commission_type', ['percentage', 'flat_rate', 'tiered', 'accelerator'])->default('percentage');
            $table->string('tier_level')->nullable(); // For tiered commission structures
            $table->decimal('base_amount', 10, 2)->default(0);
            $table->decimal('bonus_amount', 10, 2)->default(0);
            $table->json('calculation_rules')->nullable(); // Rules used for calculation
            $table->enum('payment_status', ['pending', 'approved', 'paid', 'disputed', 'cancelled'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->date('payment_period_start');
            $table->date('payment_period_end');
            $table->enum('dispute_status', ['none', 'open', 'resolved'])->default('none');
            $table->text('dispute_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_calculated_at');
            $table->timestamps();

            $table->index(['user_id', 'payment_period_start', 'payment_period_end'], 'idx_commission_user_period');
            $table->index(['deal_id', 'payment_status']);
            $table->index('payment_status');
            $table->index('dispute_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_trackings');
    }
};
