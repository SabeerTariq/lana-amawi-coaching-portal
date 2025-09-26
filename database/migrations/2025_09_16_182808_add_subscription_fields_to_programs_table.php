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
        Schema::table('programs', function (Blueprint $table) {
            $table->string('subscription_type')->nullable()->after('features');
            $table->decimal('monthly_price', 10, 2)->nullable()->after('subscription_type');
            $table->integer('monthly_sessions')->nullable()->after('monthly_price');
            $table->integer('booking_limit_per_month')->default(0)->after('monthly_sessions');
            $table->boolean('is_subscription_based')->default(false)->after('booking_limit_per_month');
            $table->json('subscription_features')->nullable()->after('is_subscription_based');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_type',
                'monthly_price',
                'monthly_sessions',
                'booking_limit_per_month',
                'is_subscription_based',
                'subscription_features'
            ]);
        });
    }
};
