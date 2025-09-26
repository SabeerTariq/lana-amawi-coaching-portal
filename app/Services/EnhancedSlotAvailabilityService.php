<?php

namespace App\Services;

use App\Models\SlotSchedule;
use App\Models\SlotException;
use Carbon\Carbon;

class EnhancedSlotAvailabilityService
{
    /**
     * Get available time slots for a specific date and booking type
     */
    public function getAvailableSlots($date, $bookingType)
    {
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        
        // Get base schedule for this day and booking type
        $schedules = SlotSchedule::active()
            ->forDay($dayOfWeek)
            ->forBookingType($bookingType)
            ->get();
        
        if ($schedules->isEmpty()) {
            return [];
        }
        
        $allSlots = [];
        
        // Generate slots from all matching schedules
        foreach ($schedules as $schedule) {
            $slots = $schedule->generateTimeSlots();
            $allSlots = array_merge($allSlots, $slots);
        }
        
        // Remove duplicates and sort
        $allSlots = array_unique($allSlots);
        sort($allSlots);
        
        // Apply exceptions
        $exceptions = SlotException::active()
            ->forDate($date)
            ->forBookingType($bookingType)
            ->get();
        
        foreach ($exceptions as $exception) {
            $allSlots = $this->applyException($allSlots, $exception, $bookingType);
        }
        
        return $allSlots;
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
     * Get all available booking types for a specific date
     */
    public function getAvailableBookingTypes($date)
    {
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        
        $schedules = SlotSchedule::active()
            ->forDay($dayOfWeek)
            ->get()
            ->pluck('booking_type')
            ->unique()
            ->toArray();
        
        // Check if any exceptions block all types
        $exceptions = SlotException::active()
            ->forDate($date)
            ->where('exception_type', 'closed')
            ->get();
        
        if ($exceptions->isNotEmpty()) {
            return [];
        }
        
        return $schedules;
    }
    
    /**
     * Get day availability for internal use (compatibility with API)
     */
    public function getDayAvailability($dayOfWeek, $bookingType = null)
    {
        $dayName = match($dayOfWeek) {
            0 => 'sunday',
            1 => 'monday', 
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            default => strtolower($dayOfWeek)
        };
        
        $query = SlotSchedule::active()->forDay($dayName);
        
        if ($bookingType) {
            $query->forBookingType($bookingType);
        }
        
        $schedules = $query->get();
        
        if ($bookingType) {
            $schedule = $schedules->first();
            return $schedule ? [
                'start' => $schedule->start_time->format('H:i'),
                'end' => $schedule->end_time->format('H:i')
            ] : null;
        }
        
        $result = [];
        foreach ($schedules as $schedule) {
            $result[$schedule->booking_type] = [
                'start' => $schedule->start_time->format('H:i'),
                'end' => $schedule->end_time->format('H:i')
            ];
        }
        
        return $result;
    }
    
    /**
     * Check if a date is available for booking
     */
    public function isDateAvailable($date)
    {
        $availableTypes = $this->getAvailableBookingTypes($date);
        return !empty($availableTypes);
    }
    
    /**
     * Get next available date for a specific booking type
     */
    public function getNextAvailableDate($bookingType, $fromDate = null)
    {
        $fromDate = $fromDate ? Carbon::parse($fromDate) : Carbon::now();
        
        for ($i = 0; $i < 30; $i++) { // Check next 30 days
            $checkDate = $fromDate->copy()->addDays($i);
            
            if ($this->isDateAvailable($checkDate->toDateString()) && 
                $this->getAvailableSlots($checkDate->toDateString(), $bookingType)) {
                return $checkDate->toDateString();
            }
        }
        
        return null;
    }
    
    /**
     * Get schedule for display (from database)
     */
    public function getScheduleForDisplay()
    {
        $schedules = SlotSchedule::active()
            ->orderBy('day_of_week')
            ->orderBy('booking_type')
            ->orderBy('start_time')
            ->get();
        
        $schedule = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        foreach ($days as $day) {
            $daySchedules = $schedules->where('day_of_week', $day);
            
            if ($daySchedules->isEmpty()) {
                $schedule[ucfirst($day)] = 'Closed';
                continue;
            }
            
            $daySchedule = [];
            foreach ($daySchedules as $scheduleItem) {
                $type = $scheduleItem->booking_type_formatted;
                $timeRange = $scheduleItem->time_range;
                
                if (!isset($daySchedule[$type])) {
                    $daySchedule[$type] = $timeRange;
                } else {
                    $daySchedule[$type] .= ', ' . $timeRange;
                }
            }
            
            $schedule[ucfirst($day)] = $daySchedule;
        }
        
        return $schedule;
    }
    
    /**
     * Apply exception to slots
     */
    private function applyException($slots, $exception, $bookingType)
    {
        if (!$exception->affectsBookingType($bookingType)) {
            return $slots;
        }
        
        switch ($exception->exception_type) {
            case 'closed':
                return []; // Remove all slots
                
            case 'blocked':
                if (!$exception->start_time || !$exception->end_time) {
                    return []; // Block entire day
                }
                
                // Remove slots within blocked time range
                return array_filter($slots, function($slot) use ($exception) {
                    return !$exception->affectsTime($slot);
                });
                
            case 'modified':
                // For modified, we might need to implement custom logic
                // For now, treat as blocked
                return array_filter($slots, function($slot) use ($exception) {
                    return !$exception->affectsTime($slot);
                });
                
            default:
                return $slots;
        }
    }
    
    /**
     * Create default schedules based on your requirements
     */
    public function createDefaultSchedules()
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
                    'max_bookings_per_slot' => 1
                ])
            );
        }
    }
    
    /**
     * Get upcoming exceptions
     */
    public function getUpcomingExceptions($days = 30)
    {
        return SlotException::active()
            ->where('exception_date', '>=', Carbon::now()->toDateString())
            ->where('exception_date', '<=', Carbon::now()->addDays($days)->toDateString())
            ->orderBy('exception_date')
            ->get();
    }
}
