<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For programs table: Copy booking_limit_per_month to monthly_sessions if monthly_sessions is null or 0
        DB::statement('UPDATE programs SET monthly_sessions = booking_limit_per_month WHERE monthly_sessions IS NULL OR monthly_sessions = 0');
        
        // For subscriptions table: Copy booking_limit_per_month to monthly_sessions if monthly_sessions is null or 0
        DB::statement('UPDATE subscriptions SET monthly_sessions = booking_limit_per_month WHERE monthly_sessions IS NULL OR monthly_sessions = 0');
        
        // Drop booking_limit_per_month from programs table
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('booking_limit_per_month');
        });
        
        // Drop booking_limit_per_month from subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('booking_limit_per_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add booking_limit_per_month back to programs table
        Schema::table('programs', function (Blueprint $table) {
            $table->integer('booking_limit_per_month')->default(0)->after('monthly_sessions');
        });
        
        // Add booking_limit_per_month back to subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('booking_limit_per_month')->default(0)->after('monthly_sessions');
        });
        
        // Copy monthly_sessions back to booking_limit_per_month
        DB::statement('UPDATE programs SET booking_limit_per_month = monthly_sessions');
        DB::statement('UPDATE subscriptions SET booking_limit_per_month = monthly_sessions');
    }
};
