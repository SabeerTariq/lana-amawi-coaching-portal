<?php

namespace App\Services;

use Carbon\Carbon;

class SlotAvailabilityService
{
    /**
     * Get available time slots for a specific date and booking type
     */
    public function getAvailableSlots($date, $bookingType)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
        
        $availability = $this->getDayAvailability($dayOfWeek, $bookingType);
        
        if (!$availability) {
            return [];
        }
        
        return $this->generateTimeSlots($availability['start'], $availability['end']);
    }
    
    /**
     * Check if a specific time slot is available for a date and booking type
     */
    public function isSlotAvailable($date, $time, $bookingType)
    {
        $availableSlots = $this->getAvailableSlots($date, $bookingType);
        return in_array($time, $availableSlots);
    }
    
    /**
     * Get availability configuration for each day of the week
     */
    private function getDayAvailability($dayOfWeek, $bookingType)
    {
        $schedule = [
            // Monday
            1 => [
                'virtual' => ['start' => '10:00', 'end' => '18:00'],
                'in-office' => ['start' => '18:00', 'end' => '21:00']
            ],
            // Tuesday
            2 => [
                'in-office' => ['start' => '08:30', 'end' => '17:00']
            ],
            // Wednesday
            3 => [
                'in-office' => ['start' => '09:00', 'end' => '12:00'],
                'virtual' => ['start' => '12:00', 'end' => '17:00']
            ],
            // Thursday
            4 => [
                'in-office' => ['start' => '09:00', 'end' => '12:00'],
                'virtual' => ['start' => '12:00', 'end' => '17:00']
            ],
            // Friday
            5 => [
                'virtual' => ['start' => '10:00', 'end' => '16:00']
            ],
            // Saturday - No availability
            6 => [],
            // Sunday - No availability
            0 => []
        ];
        
        if ($bookingType === null) {
            return $schedule[$dayOfWeek] ?? [];
        }
        
        return $schedule[$dayOfWeek][$bookingType] ?? null;
    }
    
    /**
     * Generate time slots between start and end time
     */
    private function generateTimeSlots($startTime, $endTime)
    {
        $slots = [];
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);
        
        while ($start->lt($end)) {
            $slots[] = $start->format('H:i');
            $start->addHour();
        }
        
        return $slots;
    }
    
    /**
     * Get all available booking types for a specific date
     */
    public function getAvailableBookingTypes($date)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = $this->getDayAvailability($dayOfWeek, null);
        
        if (!$schedule) {
            return [];
        }
        
        return array_keys($schedule);
    }
    
    /**
     * Get formatted schedule for display
     */
    public function getScheduleForDisplay()
    {
        return [
            'Monday' => [
                'Virtual' => '10:00 AM - 6:00 PM',
                'In-Office' => '6:00 PM - 9:00 PM'
            ],
            'Tuesday' => [
                'In-Office' => '8:30 AM - 5:00 PM'
            ],
            'Wednesday' => [
                'In-Office' => '9:00 AM - 12:00 PM',
                'Virtual' => '12:00 PM - 5:00 PM'
            ],
            'Thursday' => [
                'In-Office' => '9:00 AM - 12:00 PM',
                'Virtual' => '12:00 PM - 5:00 PM'
            ],
            'Friday' => [
                'Virtual' => '10:00 AM - 4:00 PM'
            ],
            'Saturday' => 'Closed',
            'Sunday' => 'Closed'
        ];
    }
    
    /**
     * Check if a date is available for booking
     */
    public function isDateAvailable($date)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = $this->getDayAvailability($dayOfWeek, null);
        
        return !empty($schedule);
    }
    
    /**
     * Get next available date for a specific booking type
     */
    public function getNextAvailableDate($bookingType, $fromDate = null)
    {
        $fromDate = $fromDate ? Carbon::parse($fromDate) : Carbon::now();
        
        for ($i = 0; $i < 14; $i++) { // Check next 2 weeks
            $checkDate = $fromDate->copy()->addDays($i);
            
            if ($this->isDateAvailable($checkDate->toDateString()) && 
                $this->getAvailableSlots($checkDate->toDateString(), $bookingType)) {
                return $checkDate->toDateString();
            }
        }
        
        return null;
    }
}
