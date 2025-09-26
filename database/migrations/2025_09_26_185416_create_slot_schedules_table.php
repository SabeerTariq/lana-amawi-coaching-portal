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
        Schema::create('slot_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // e.g., "Default Schedule", "Holiday Schedule"
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->enum('booking_type', ['in-office', 'virtual']);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(60); // Duration in minutes
            $table->integer('break_duration')->default(0); // Break between slots in minutes
            $table->boolean('is_active')->default(true);
            $table->integer('max_bookings_per_slot')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure no duplicate schedules for same day/type
            $table->unique(['day_of_week', 'booking_type', 'start_time'], 'unique_day_type_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slot_schedules');
    }
};
