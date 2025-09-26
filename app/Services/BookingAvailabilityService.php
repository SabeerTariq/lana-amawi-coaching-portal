<?php

namespace App\Services;

use App\Models\SlotSchedule;
use App\Models\SlotException;
use App\Models\Appointment;
use App\Models\Booking;
use Carbon\Carbon;

class BookingAvailabilityService
{
    protected $slotService;

    public function __construct(EnhancedSlotAvailabilityService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Get available slots for booking (considering existing bookings)
     */
    public function getAvailableSlotsForBooking($date, $bookingType)
    {
        // First get slots from schedule
        $scheduledSlots = $this->slotService->getAvailableSlots($date, $bookingType);
        
        if (empty($scheduledSlots)) {
            return [];
        }

        // Get existing bookings and appointments for this date and type
        $existingBookings = $this->getExistingBookings($date, $bookingType);
        
        // Filter out booked slots
        $availableSlots = array_filter($scheduledSlots, function($slot) use ($existingBookings) {
            return !in_array($slot, $existingBookings);
        });

        return array_values($availableSlots);
    }

    /**
     * Check if a specific slot is available for booking
     */
    public function isSlotAvailableForBooking($date, $time, $bookingType, $excludeBookingId = null)
    {
        // Check if slot exists in schedule
        if (!$this->slotService->isSlotAvailable($date, $time, $bookingType)) {
            return false;
        }

        // Check if slot is already booked
        $existingBookings = $this->getExistingBookings($date, $bookingType, $excludeBookingId);
        
        return !in_array($time, $existingBookings);
    }

    /**
     * Get existing bookings and appointments for a specific date and booking type
     */
    public function getExistingBookings($date, $bookingType, $excludeBookingId = null)
    {
        $bookedSlots = [];

        // Get confirmed appointments
        $appointments = Appointment::whereDate('appointment_date', $date)
            ->where('booking_type', $bookingType)
            ->where('status', 'confirmed')
            ->get();

        foreach ($appointments as $appointment) {
            $bookedSlots[] = Carbon::parse($appointment->appointment_time)->format('H:i');
        }

        // Get pending bookings (not yet converted to appointments)
        $bookingsQuery = Booking::whereDate('preferred_date', $date)
            ->where('booking_type', $bookingType)
            ->whereIn('status', ['pending', 'accepted']);

        if ($excludeBookingId) {
            $bookingsQuery->where('id', '!=', $excludeBookingId);
        }

        $bookings = $bookingsQuery->get();

        foreach ($bookings as $booking) {
            $bookedSlots[] = Carbon::parse($booking->preferred_time)->format('H:i');
        }

        return array_unique($bookedSlots);
    }

    /**
     * Get next available date for a specific booking type
     */
    public function getNextAvailableDate($bookingType, $fromDate = null, $maxDays = 30)
    {
        $fromDate = $fromDate ? Carbon::parse($fromDate) : Carbon::now();
        
        for ($i = 0; $i < $maxDays; $i++) {
            $checkDate = $fromDate->copy()->addDays($i);
            $availableSlots = $this->getAvailableSlotsForBooking($checkDate->toDateString(), $bookingType);
            
            if (!empty($availableSlots)) {
                return $checkDate->toDateString();
            }
        }
        
        return null;
    }

    /**
     * Get availability for a date range
     */
    public function getAvailabilityForDateRange($startDate, $endDate, $bookingType = null)
    {
        $availability = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dateString = $current->format('Y-m-d');
            
            if ($bookingType) {
                $slots = $this->getAvailableSlotsForBooking($dateString, $bookingType);
                $availability[] = [
                    'date' => $dateString,
                    'day_name' => $current->format('l'),
                    'booking_type' => $bookingType,
                    'available_slots' => $slots,
                    'slot_count' => count($slots),
                    'is_available' => count($slots) > 0
                ];
            } else {
                $virtualSlots = $this->getAvailableSlotsForBooking($dateString, 'virtual');
                $officeSlots = $this->getAvailableSlotsForBooking($dateString, 'in-office');
                
                $availability[] = [
                    'date' => $dateString,
                    'day_name' => $current->format('l'),
                    'virtual_slots' => $virtualSlots,
                    'in_office_slots' => $officeSlots,
                    'virtual_count' => count($virtualSlots),
                    'office_count' => count($officeSlots),
                    'total_count' => count($virtualSlots) + count($officeSlots),
                    'is_available' => count($virtualSlots) > 0 || count($officeSlots) > 0
                ];
            }
            
            $current->addDay();
        }

        return $availability;
    }

    /**
     * Validate booking request
     */
    public function validateBookingRequest($date, $time, $bookingType, $excludeBookingId = null)
    {
        $errors = [];

        // Check if date is in the future
        if (Carbon::parse($date)->isPast()) {
            $errors[] = 'Cannot book appointments in the past.';
        }

        // Check if slot is available
        if (!$this->isSlotAvailableForBooking($date, $time, $bookingType, $excludeBookingId)) {
            $errors[] = 'The selected time slot is no longer available.';
        }

        // Check if date is available for booking type
        $availableTypes = $this->slotService->getAvailableBookingTypes($date);
        if (!in_array($bookingType, $availableTypes)) {
            $errors[] = 'No availability for ' . ucfirst($bookingType) . ' appointments on this date.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get booking statistics for admin
     */
    public function getBookingStatistics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        $stats = [
            'total_appointments' => Appointment::whereBetween('appointment_date', [$startDate, $endDate])->count(),
            'total_bookings' => Booking::whereBetween('preferred_date', [$startDate, $endDate])->count(),
            'virtual_appointments' => Appointment::whereBetween('appointment_date', [$startDate, $endDate])
                ->where('booking_type', 'virtual')->count(),
            'office_appointments' => Appointment::whereBetween('appointment_date', [$startDate, $endDate])
                ->where('booking_type', 'in-office')->count(),
            'pending_bookings' => Booking::whereBetween('preferred_date', [$startDate, $endDate])
                ->where('status', 'pending')->count(),
            'accepted_bookings' => Booking::whereBetween('preferred_date', [$startDate, $endDate])
                ->where('status', 'accepted')->count(),
        ];

        return $stats;
    }

    /**
     * Get upcoming availability summary
     */
    public function getUpcomingAvailabilitySummary($days = 7)
    {
        $summary = [];
        $startDate = Carbon::now()->addDay();
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');
            
            $virtualSlots = $this->getAvailableSlotsForBooking($dateString, 'virtual');
            $officeSlots = $this->getAvailableSlotsForBooking($dateString, 'in-office');
            
            $summary[] = [
                'date' => $dateString,
                'day_name' => $date->format('l'),
                'virtual_available' => count($virtualSlots),
                'office_available' => count($officeSlots),
                'total_available' => count($virtualSlots) + count($officeSlots),
                'has_availability' => count($virtualSlots) > 0 || count($officeSlots) > 0
            ];
        }

        return $summary;
    }
}
