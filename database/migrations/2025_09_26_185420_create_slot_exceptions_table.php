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
        Schema::create('slot_exceptions', function (Blueprint $table) {
            $table->id();
            $table->date('exception_date');
            $table->enum('booking_type', ['in-office', 'virtual', 'both']);
            $table->time('start_time')->nullable(); // If null, applies to entire day
            $table->time('end_time')->nullable(); // If null, applies to entire day
            $table->enum('exception_type', ['blocked', 'modified', 'closed']); // blocked = no bookings, modified = different times, closed = entire day closed
            $table->text('reason')->nullable(); // Reason for exception
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index for efficient date-based queries
            $table->index(['exception_date', 'booking_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_exceptions');
    }
};
