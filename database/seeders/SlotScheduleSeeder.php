<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SlotSchedule;

class SlotScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSchedules = [
            // Monday
            ['day_of_week' => 'monday', 'booking_type' => 'virtual', 'start_time' => '10:00', 'end_time' => '18:00'],
            ['day_of_week' => 'monday', 'booking_type' => 'in-office', 'start_time' => '18:00', 'end_time' => '21:00'],
            
            // Tuesday
            ['day_of_week' => 'tuesday', 'booking_type' => 'in-office', 'start_time' => '08:30', 'end_time' => '17:00'],
            
            // Wednesday
            ['day_of_week' => 'wednesday', 'booking_type' => 'in-office', 'start_time' => '09:00', 'end_time' => '12:00'],
            ['day_of_week' => 'wednesday', 'booking_type' => 'virtual', 'start_time' => '12:00', 'end_time' => '17:00'],
            
            // Thursday
            ['day_of_week' => 'thursday', 'booking_type' => 'in-office', 'start_time' => '09:00', 'end_time' => '12:00'],
            ['day_of_week' => 'thursday', 'booking_type' => 'virtual', 'start_time' => '12:00', 'end_time' => '17:00'],
            
            // Friday
            ['day_of_week' => 'friday', 'booking_type' => 'virtual', 'start_time' => '10:00', 'end_time' => '16:00'],
        ];
        
        foreach ($defaultSchedules as $scheduleData) {
            SlotSchedule::firstOrCreate(
                [
                    'day_of_week' => $scheduleData['day_of_week'],
                    'booking_type' => $scheduleData['booking_type'],
                    'start_time' => $scheduleData['start_time']
                ],
                array_merge($scheduleData, [
                    'name' => 'Default Schedule',
                    'slot_duration' => 60,
                    'break_duration' => 0,
                    'is_active' => true,
                    'max_bookings_per_slot' => 1,
                    'notes' => 'Default availability schedule'
                ])
            );
        }
    }
}
