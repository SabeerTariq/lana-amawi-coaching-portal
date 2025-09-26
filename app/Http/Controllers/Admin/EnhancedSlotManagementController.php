<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EnhancedSlotAvailabilityService;
use App\Models\SlotSchedule;
use App\Models\SlotException;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EnhancedSlotManagementController extends Controller
{
    protected $slotService;

    public function __construct(EnhancedSlotAvailabilityService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Display enhanced slot management dashboard
     */
    public function index()
    {
        $schedules = SlotSchedule::active()->orderBy('day_of_week')->orderBy('booking_type')->orderBy('start_time')->get();
        $exceptions = $this->slotService->getUpcomingExceptions(30);
        $scheduleDisplay = $this->slotService->getScheduleForDisplay();
        
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

        return view('admin.enhanced-slot-management', compact('schedules', 'exceptions', 'scheduleDisplay', 'next7Days'));
    }

    /**
     * Show schedule management
     */
    public function schedules()
    {
        $schedules = SlotSchedule::orderBy('day_of_week')->orderBy('booking_type')->orderBy('start_time')->get();
        return view('admin.slot-schedules', compact('schedules'));
    }

    /**
     * Show exceptions management
     */
    public function exceptions()
    {
        $exceptions = SlotException::orderBy('exception_date', 'desc')->paginate(20);
        return view('admin.slot-exceptions', compact('exceptions'));
    }

    /**
     * Store a new schedule
     */
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'booking_type' => 'required|in:in-office,virtual',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:240',
            'break_duration' => 'integer|min:0|max:60',
            'max_bookings_per_slot' => 'integer|min:1|max:10',
            'notes' => 'nullable|string|max:1000'
        ]);

        SlotSchedule::create($request->all());

        return redirect()->route('admin.slot-management.schedules')
            ->with('success', 'Schedule created successfully!');
    }

    /**
     * Update a schedule
     */
    public function updateSchedule(Request $request, SlotSchedule $schedule)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'booking_type' => 'required|in:in-office,virtual',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:240',
            'break_duration' => 'integer|min:0|max:60',
            'max_bookings_per_slot' => 'integer|min:1|max:10',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000'
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.slot-management.schedules')
            ->with('success', 'Schedule updated successfully!');
    }

    /**
     * Delete a schedule
     */
    public function deleteSchedule(SlotSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.slot-management.schedules')
            ->with('success', 'Schedule deleted successfully!');
    }

    /**
     * Store a new exception
     */
    public function storeException(Request $request)
    {
        $request->validate([
            'exception_date' => 'required|date|after_or_equal:today',
            'booking_type' => 'required|in:in-office,virtual,both',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'exception_type' => 'required|in:blocked,modified,closed',
            'reason' => 'nullable|string|max:500'
        ]);

        SlotException::create($request->all());

        return redirect()->route('admin.slot-management.exceptions')
            ->with('success', 'Exception created successfully!');
    }

    /**
     * Update an exception
     */
    public function updateException(Request $request, SlotException $exception)
    {
        $request->validate([
            'exception_date' => 'required|date',
            'booking_type' => 'required|in:in-office,virtual,both',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'exception_type' => 'required|in:blocked,modified,closed',
            'reason' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $exception->update($request->all());

        return redirect()->route('admin.slot-management.exceptions')
            ->with('success', 'Exception updated successfully!');
    }

    /**
     * Delete an exception
     */
    public function deleteException(SlotException $exception)
    {
        $exception->delete();
        return redirect()->route('admin.slot-management.exceptions')
            ->with('success', 'Exception deleted successfully!');
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
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

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

    /**
     * Bulk operations for schedules
     */
    public function bulkScheduleOperation(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:slot_schedules,id'
        ]);

        $schedules = SlotSchedule::whereIn('id', $request->schedule_ids);

        switch ($request->action) {
            case 'activate':
                $schedules->update(['is_active' => true]);
                $message = 'Selected schedules activated successfully!';
                break;
            case 'deactivate':
                $schedules->update(['is_active' => false]);
                $message = 'Selected schedules deactivated successfully!';
                break;
            case 'delete':
                $schedules->delete();
                $message = 'Selected schedules deleted successfully!';
                break;
        }

        return redirect()->route('admin.slot-management.schedules')
            ->with('success', $message);
    }
}
