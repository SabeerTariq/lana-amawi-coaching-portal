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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id')->nullable()->after('payment_reference');
            $table->string('stripe_charge_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_customer_id')->nullable()->after('stripe_charge_id');
            $table->index('stripe_payment_intent_id');
            $table->index('stripe_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['stripe_payment_intent_id']);
            $table->dropIndex(['stripe_customer_id']);
            $table->dropColumn(['stripe_payment_intent_id', 'stripe_charge_id', 'stripe_customer_id']);
        });
    }
};
