<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingAvailabilityService;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    protected $bookingService;

    public function __construct(BookingAvailabilityService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Get available time slots for a specific date and booking type
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'type' => 'required|string|in:in-office,virtual'
        ]);

        $date = $request->input('date');
        $type = $request->input('type');

        try {
            // Use the slot service directly
            $slotService = app(\App\Services\EnhancedSlotAvailabilityService::class);
            
            // Check if the date is available for booking
            if (!$slotService->isDateAvailable($date)) {
                return response()->json([
                    'slots' => [],
                    'date' => $date,
                    'type' => $type,
                    'message' => 'No availability for this date'
                ]);
            }

            // Get available slots for the specific booking type
            $slots = $slotService->getAvailableSlots($date, $type);

            return response()->json([
                'slots' => $slots,
                'date' => $date,
                'type' => $type,
                'message' => count($slots) > 0 ? 'Slots available' : 'No slots available for this session type'
            ]);
        } catch (\Exception $e) {
            \Log::error('API getAvailableSlots error: ' . $e->getMessage());
            return response()->json([
                'slots' => [],
                'date' => $date,
                'type' => $type,
                'error' => 'Error retrieving slots: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available booking types for a specific date
     */
    public function getAvailableBookingTypes(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today'
        ]);

        $date = $request->input('date');

        if (!$this->bookingService->slotService->isDateAvailable($date)) {
            return response()->json([
                'types' => [],
                'message' => 'No availability for this date'
            ]);
        }

        $types = $this->bookingService->slotService->getAvailableBookingTypes($date);

        return response()->json([
            'types' => $types,
            'date' => $date,
            'message' => 'Available booking types retrieved'
        ]);
    }

    /**
     * Get the full schedule for display
     */
    public function getSchedule()
    {
        $schedule = $this->bookingService->slotService->getScheduleForDisplay();

        return response()->json([
            'schedule' => $schedule,
            'message' => 'Schedule retrieved successfully'
        ]);
    }

    /**
     * Check if a specific slot is available
     */
    public function checkSlotAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'type' => 'required|string|in:in-office,virtual'
        ]);

        $date = $request->input('date');
        $time = $request->input('time');
        $type = $request->input('type');

        $isAvailable = $this->bookingService->isSlotAvailableForBooking($date, $time, $type);

        return response()->json([
            'available' => $isAvailable,
            'date' => $date,
            'time' => $time,
            'type' => $type,
            'message' => $isAvailable ? 'Slot is available' : 'Slot is not available'
        ]);
    }
}
