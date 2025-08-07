<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Message;
use App\Models\Booking;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalClients = User::where('is_admin', false)->count();
        $appointmentsToday = Appointment::where('appointment_date', now()->toDateString())->count();
        $unreadMessages = Message::where('sender_type', 'client')
            ->where('is_read', false)
            ->count();
        $totalRevenue = Appointment::where('status', 'completed')->count() * 100; // Assuming $100 per session

        $todayAppointments = Appointment::with('user')
            ->where('appointment_date', now()->toDateString())
            ->orderBy('appointment_time')
            ->get();

        $recentActivity = collect(); // You would implement activity logging here

        return view('admin.dashboard', compact(
            'totalClients',
            'appointmentsToday',
            'unreadMessages',
            'totalRevenue',
            'todayAppointments',
            'recentActivity'
        ));
    }

    public function clients()
    {
        $clients = User::where('is_admin', false)
            ->withCount(['appointments', 'messages'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.clients', compact('clients'));
    }

    public function clientProfile(User $client)
    {
        $appointments = Appointment::where('user_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $messages = Message::where('user_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.client-profile', compact('client', 'appointments', 'messages'));
    }

    public function messages()
    {
        $clients = User::where('is_admin', false)
            ->withCount(['messages' => function($query) {
                $query->where('is_read', false);
            }])
            ->orderBy('name')
            ->get();

        $selectedClient = null;
        $messages = collect();

        if (request('client_id')) {
            $selectedClient = User::find(request('client_id'));
            $messages = Message::where('user_id', $selectedClient->id)
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Mark client messages as read when admin views them
            Message::where('user_id', $selectedClient->id)
                ->where('sender_type', 'client')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('admin.messages', compact('clients', 'selectedClient', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
        ]);

        $messageData = [
            'user_id' => $request->user_id,
            'message' => $request->message ?? '',
            'sender_type' => 'admin',
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
        if (!Auth::user()->is_admin && Auth::user()->id !== $message->user_id) {
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

    public function calendar()
    {
        $appointments = Appointment::with('user')
            ->where('appointment_date', '>=', now()->subDays(7))
            ->where('appointment_date', '<=', now()->addDays(30))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $clients = User::where('is_admin', false)->orderBy('name')->get();
        
        $todayAppointments = Appointment::where('appointment_date', today())->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();

        return view('admin.calendar', compact('appointments', 'clients', 'todayAppointments', 'pendingAppointments'));
    }

    public function getAppointment($id)
    {
        $appointment = Appointment::with('user')->findOrFail($id);
        return response()->json($appointment);
    }

    public function storeAppointment(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create([
            'user_id' => $request->client_id,
            'program' => null, // Program field is nullable
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => $request->status,
            'message' => $request->notes, // Use 'message' field instead of 'notes'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully',
            'appointment' => $appointment->load('user')
        ]);
    }

    public function rescheduleAppointment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
        ]);

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully'
        ]);
    }



    public function exportCalendar()
    {
        $appointments = Appointment::with('user')
            ->where('appointment_date', '>=', now()->subDays(30))
            ->where('appointment_date', '<=', now()->addDays(90))
            ->get();

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Lana Amawi Coaching//Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";

        foreach ($appointments as $appointment) {
            $startDate = $appointment->appointment_date->format('Ymd') . 'T' . str_replace(':', '', $appointment->appointment_time) . '00';
            $endDate = $appointment->appointment_date->format('Ymd') . 'T' . str_replace(':', '', $appointment->appointment_time) . '00';
            
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . $appointment->id . "@lana-amawi.com\r\n";
            $ical .= "DTSTART:" . $startDate . "\r\n";
            $ical .= "DTEND:" . $endDate . "\r\n";
            $ical .= "SUMMARY:Coaching Session - " . $appointment->user->name . "\r\n";
            $ical .= "DESCRIPTION:" . ($appointment->notes ?? 'Coaching session') . "\r\n";
            $ical .= "STATUS:" . strtoupper($appointment->status) . "\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        return response($ical)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="appointments.ics"');
    }

    public function settings()
    {
        $admin = Auth::user();
        
        return view('admin.settings', compact('admin'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'timezone' => 'nullable|string',
            'email_template' => 'nullable|string',
        ]);

        $admin = Auth::user();
        $admin->update($request->only(['name', 'email']));

        // Update settings in config or database
        // You might want to store additional settings in a separate table

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    public function appointments()
    {
        $appointments = Appointment::with('user')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(20);

        return view('admin.appointments', compact('appointments'));
    }

    public function bookings()
    {
        $bookings = Booking::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings', compact('bookings'));
    }

    public function convertBookingToAppointment(Booking $booking)
    {
        // Create appointment from booking
        $appointment = Appointment::create([
            'user_id' => User::where('email', $booking->email)->first()->id,
            'program' => $booking->program ?? null, // Handle null program values
            'appointment_date' => $booking->preferred_date,
            'appointment_time' => $booking->preferred_time,
            'message' => $booking->message,
            'status' => 'confirmed',
        ]);

        // Update booking status
        $booking->update(['status' => 'confirmed']);

        // Send confirmation email to client
        // You can implement email notification here

        return redirect()->back()->with('success', 'Booking converted to appointment successfully!');
    }

    public function suggestAlternativeTime(Request $request, Booking $booking)
    {
        $request->validate([
            'suggested_date' => 'required|date|after:today',
            'suggested_time' => 'required|string',
            'message' => 'nullable|string',
        ]);

        // Update booking with suggested time
        $booking->update([
            'preferred_date' => $request->suggested_date,
            'preferred_time' => $request->suggested_time,
            'message' => $request->message,
            'status' => 'suggested_alternative',
        ]);

        // Send email to client with suggested time
        // You can implement email notification here

        return redirect()->back()->with('success', 'Alternative time suggested to client!');
    }

    public function confirmAppointment(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Appointment confirmed successfully!');
    }

    public function completeAppointment(Appointment $appointment)
    {
        $appointment->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Appointment marked as completed successfully!');
    }

    public function cancelAppointment(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Appointment cancelled successfully!');
    }
} 