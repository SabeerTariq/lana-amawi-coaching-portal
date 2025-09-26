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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->string('subscription_type');
            $table->decimal('monthly_price', 10, 2);
            $table->integer('monthly_sessions');
            $table->integer('booking_limit_per_month');
            $table->boolean('is_active')->default(true);
            $table->datetime('starts_at');
            $table->datetime('ends_at')->nullable();
            $table->datetime('next_billing_date')->nullable();
            $table->datetime('last_billing_date')->nullable();
            $table->integer('total_bookings_this_month')->default(0);
            $table->json('subscription_features')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'is_active']);
            $table->index(['program_id', 'is_active']);
            $table->index('next_billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
