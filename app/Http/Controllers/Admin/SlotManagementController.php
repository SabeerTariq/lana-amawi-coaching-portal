<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SlotAvailabilityService;
use Illuminate\Http\Request;

class SlotManagementController extends Controller
{
    protected $slotService;

    public function __construct(SlotAvailabilityService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Display slot management dashboard
     */
    public function index()
    {
        $schedule = $this->slotService->getScheduleForDisplay();
        
        // Get next 7 days availability
        $next7Days = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = now()->addDays($i);
            $dayName = $date->format('l');
            $dateString = $date->format('Y-m-d');
            
            $next7Days[] = [
                'date' => $dateString,
                'day_name' => $dayName,
                'virtual_slots' => $this->slotService->getAvailableSlots($dateString, 'virtual'),
                'in_office_slots' => $this->slotService->getAvailableSlots($dateString, 'in-office'),
                'available_types' => $this->slotService->getAvailableBookingTypes($dateString)
            ];
        }

        return view('admin.slot-management', compact('schedule', 'next7Days'));
    }

    /**
     * Get availability for a specific date range
     */
    public function getAvailability(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'booking_type' => 'nullable|string|in:in-office,virtual'
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $bookingType = $request->input('booking_type');

        $availability = [];
        $current = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dateString = $current->format('Y-m-d');
            
            if ($bookingType) {
                $slots = $this->slotService->getAvailableSlots($dateString, $bookingType);
                $availability[] = [
                    'date' => $dateString,
                    'day_name' => $current->format('l'),
                    'booking_type' => $bookingType,
                    'slots' => $slots,
                    'slot_count' => count($slots)
                ];
            } else {
                $availability[] = [
                    'date' => $dateString,
                    'day_name' => $current->format('l'),
                    'virtual_slots' => $this->slotService->getAvailableSlots($dateString, 'virtual'),
                    'in_office_slots' => $this->slotService->getAvailableSlots($dateString, 'in-office'),
                    'available_types' => $this->slotService->getAvailableBookingTypes($dateString)
                ];
            }
            
            $current->addDay();
        }

        return response()->json([
            'availability' => $availability,
            'message' => 'Availability retrieved successfully'
        ]);
    }

    /**
     * Check specific slot availability
     */
    public function checkSlot(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'booking_type' => 'required|string|in:in-office,virtual'
        ]);

        $isAvailable = $this->slotService->isSlotAvailable(
            $request->input('date'),
            $request->input('time'),
            $request->input('booking_type')
        );

        return response()->json([
            'available' => $isAvailable,
            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'booking_type' => $request->input('booking_type'),
            'message' => $isAvailable ? 'Slot is available' : 'Slot is not available'
        ]);
    }
}
