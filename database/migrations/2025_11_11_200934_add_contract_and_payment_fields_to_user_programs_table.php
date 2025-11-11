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
        Schema::table('user_programs', function (Blueprint $table) {
            $table->integer('contract_duration_months')->default(3)->after('status');
            $table->enum('payment_type', ['monthly', 'one_time'])->nullable()->after('contract_duration_months');
            $table->date('contract_start_date')->nullable()->after('payment_type');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->date('next_payment_date')->nullable()->after('contract_end_date');
            $table->integer('total_payments_due')->default(3)->after('next_payment_date');
            $table->integer('payments_completed')->default(0)->after('total_payments_due');
            $table->decimal('one_time_payment_amount', 10, 2)->nullable()->after('payments_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_programs', function (Blueprint $table) {
            $table->dropColumn([
                'contract_duration_months',
                'payment_type',
                'contract_start_date',
                'contract_end_date',
                'next_payment_date',
                'total_payments_due',
                'payments_completed',
                'one_time_payment_amount',
            ]);
        });
    }
};
