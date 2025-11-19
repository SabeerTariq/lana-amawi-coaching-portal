<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Message;
use App\Models\Booking;
use App\Models\UserProgram;
use App\Models\Payment;
use App\Services\BookingAvailabilityService;

class ClientController extends Controller
{
    protected $bookingService;

    public function __construct(BookingAvailabilityService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    public function dashboard()
    {
        $user = Auth::user();
        
        $nextAppointment = Appointment::where('user_id', $user->id)
            ->upcoming()
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        $recentMessages = Message::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $totalAppointments = Appointment::where('user_id', $user->id)->count();
        $totalMessages = Message::where('user_id', $user->id)->count();
        $hoursCoached = Appointment::where('user_id', $user->id)
            ->completed()
            ->count() * 1; // Assuming 1 hour per session

        return view('client.dashboard', compact(
            'nextAppointment',
            'recentMessages',
            'totalAppointments',
            'totalMessages',
            'hoursCoached'
        ));
    }

    public function appointments()
    {
        $user = Auth::user();
        
        // Get pending bookings for this user
        $pendingBookings = Booking::where('email', $user->email)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get suggested alternative time bookings
        $suggestedBookings = Booking::where('email', $user->email)
            ->where('status', 'suggested_alternative')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $upcomingAppointments = Appointment::where('user_id', $user->id)
            ->upcoming()
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $pastAppointments = Appointment::where('user_id', $user->id)
            ->past()
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('client.appointments', compact(
            'pendingBookings', 
            'suggestedBookings',
            'upcomingAppointments', 
            'pastAppointments'
        ));
    }

    public function messages()
    {
        $user = Auth::user();
        
        $messages = Message::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Mark admin messages as read when client views them
        Message::where('user_id', $user->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('client.messages', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
        ]);

        $user = Auth::user();

        $messageData = [
            'user_id' => $user->id,
            'message' => $request->message ?? '',
            'sender_type' => 'client',
        ];

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('message-attachments', $fileName, 'public');
            
            $messageData['attachment_path'] = $filePath;
            $messageData['attachment_name'] = $file->getClientOriginalName();
            $messageData['attachment_type'] = $file->getMimeType();
            $messageData['attachment_size'] = $file->getSize();
        }

        // Only create message if there's a message text or attachment
        if (!empty($request->message) || $request->hasFile('attachment')) {
            Message::create($messageData);
            return redirect()->back()->with('success', 'Message sent successfully!');
        }

        return redirect()->back()->with('error', 'Please provide a message or attachment.');
    }

    public function downloadAttachment(Message $message)
    {
        // Check if user has permission to access this message
        if (Auth::user()->id !== $message->user_id) {
            abort(403, 'Access denied.');
        }

        if (!$message->hasAttachment()) {
            abort(404, 'No attachment found.');
        }

        $filePath = storage_path('app/public/' . $message->attachment_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $message->attachment_name);
    }

    public function profile()
    {
        $user = Auth::user();
        
        $appointments = Appointment::where('user_id', $user->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('client.profile', compact('user', 'appointments'));
    }

    public function rescheduleAppointment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'new_date' => 'required|date|after:today',
            'new_time' => 'required|string',
        ]);

        // Check if user owns this appointment
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $appointment->update([
            'appointment_date' => $request->new_date,
            'appointment_time' => $request->new_time,
            'status' => 'pending', // Reset to pending for admin approval
        ]);

        return redirect()->back()->with('success', 'Appointment rescheduled successfully!');
    }

    public function cancelAppointment(Appointment $appointment)
    {
        // Check if user owns this appointment
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Appointment cancelled successfully!');
    }

    public function bookNewSession(Request $request)
    {
        $request->validate([
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string',
            'booking_type' => 'required|string|in:in-office,virtual',
            'message' => 'nullable|string|max:1000',
        ]);

        // Validate slot availability using the booking service
        $validation = $this->bookingService->validateBookingRequest(
            $request->preferred_date, 
            $request->preferred_time, 
            $request->booking_type
        );

        if (!$validation['valid']) {
            return redirect()->back()->with('error', implode(' ', $validation['errors']));
        }

        $user = Auth::user();

        // Check if user has any active programs
        $activePrograms = $user->userPrograms()
            ->where('status', UserProgram::STATUS_ACTIVE)
            ->with('program')
            ->get();

        if ($activePrograms->isEmpty()) {
            return redirect()->back()->with('error', 'You must have an approved and active program before booking sessions. Please select a program first.');
        }

        try {
            $booking = Booking::create([
                'full_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'program' => null,
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time,
                'booking_type' => $request->booking_type,
                'message' => $request->message,
                'status' => 'pending',
            ]);
            return redirect()->back()->with('success', 'Your new session has been booked successfully! We will review and confirm your appointment.');
        } catch (\Exception $e) {
            \Log::error('Client booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while booking your session. Please try again.');
        }
    }

    /**
     * Client accepts suggested alternative time
     */
    public function acceptSuggestedTime(Request $request, Booking $booking)
    {
        // Verify the booking belongs to the authenticated user
        if ($booking->email !== Auth::user()->email) {
            abort(403, 'Access denied.');
        }

        // Verify the booking is in suggested_alternative status
        if ($booking->status !== 'suggested_alternative') {
            return redirect()->back()->with('error', 'This booking is not available for acceptance.');
        }

        $booking->update([
            'status' => 'accepted',
            'client_response' => 'Client accepted the suggested alternative time.',
            'response_date' => now(),
        ]);

        return redirect()->back()->with('success', 'You have accepted the suggested alternative time. We will convert this to an appointment shortly.');
    }

    /**
     * Client rejects suggested alternative time
     */
    public function rejectSuggestedTime(Request $request, Booking $booking)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // Verify the booking belongs to the authenticated user
        if ($booking->email !== Auth::user()->email) {
            abort(403, 'Access denied.');
        }

        // Verify the booking is in suggested_alternative status
        if ($booking->status !== 'suggested_alternative') {
            return redirect()->back()->with('error', 'This booking is not available for rejection.');
        }

        $booking->update([
            'status' => 'rejected',
            'client_response' => 'Client rejected the suggested time. Reason: ' . $request->rejection_reason,
            'response_date' => now(),
        ]);

        return redirect()->back()->with('success', 'You have rejected the suggested alternative time. We will contact you to arrange a different time.');
    }

    /**
     * Client requests modification to suggested alternative time
     */
    public function modifySuggestedTime(Request $request, Booking $booking)
    {
        $request->validate([
            'new_date' => 'required|date|after:today',
            'new_time' => 'required|string',
            'modification_reason' => 'required|string|max:500',
        ]);

        // Verify the booking belongs to the authenticated user
        if ($booking->email !== Auth::user()->email) {
            abort(403, 'Access denied.');
        }

        // Verify the booking is in suggested_alternative status
        if ($booking->status !== 'suggested_alternative') {
            return redirect()->back()->with('error', 'This booking is not available for modification.');
        }

        $booking->update([
            'status' => 'modified',
            'preferred_date' => $request->new_date,
            'preferred_time' => $request->new_time,
            'client_response' => 'Client requested modification. New preference: ' . $request->new_date . ' at ' . $request->new_time . '. Reason: ' . $request->modification_reason,
            'response_date' => now(),
        ]);

        return redirect()->back()->with('success', 'You have requested a modification to the suggested time. We will review your request and get back to you.');
    }

    /**
     * Show client subscriptions and payments
     */
    public function subscriptions()
    {
        $user = Auth::user();
        
        // Get all user programs with their payments
        $userPrograms = UserProgram::where('user_id', $user->id)
            ->with(['program', 'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get all payments
        $payments = Payment::whereHas('userProgram', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('userProgram.program')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate totals
        $totalPaid = $payments->where('status', Payment::STATUS_COMPLETED)->sum('amount');
        $activeSubscriptions = $userPrograms->where('status', UserProgram::STATUS_ACTIVE)
            ->where('payment_type', UserProgram::PAYMENT_TYPE_MONTHLY)
            ->count();
        
        return view('client.subscriptions', compact('userPrograms', 'payments', 'totalPaid', 'activeSubscriptions'));
    }
} 