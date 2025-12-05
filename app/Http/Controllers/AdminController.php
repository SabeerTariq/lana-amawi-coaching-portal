<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Message;
use App\Models\Booking;
use App\Models\ClientNote; // Added this import
use App\Models\UserProgram;
use App\Models\Payment;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalClients = User::where('is_admin', false)->count();
        $appointmentsToday = Appointment::where('appointment_date', now()->toDateString())->count();
        $unreadMessages = Message::where('sender_type', 'client')
            ->where('is_read', false)
            ->count();
        $totalRevenue = Payment::where('status', Payment::STATUS_COMPLETED)->sum('amount') ?? 0;

        $todayAppointments = Appointment::with('user')
            ->where('appointment_date', now()->toDateString())
            ->orderBy('appointment_time')
            ->get();

        // Get appointment status counts for the chart
        $appointmentStatusCounts = [
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        // Get weekly appointment data for the line chart
        $weeklyAppointments = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Appointment::where('appointment_date', $date->toDateString())->count();
            $weeklyAppointments[] = [
                'date' => $date->format('D'),
                'count' => $count
            ];
        }

        $recentActivity = collect(); // You would implement activity logging here

        return view('admin.dashboard', compact(
            'totalClients',
            'appointmentsToday',
            'unreadMessages',
            'totalRevenue',
            'todayAppointments',
            'appointmentStatusCounts',
            'weeklyAppointments',
            'recentActivity'
        ));
    }

    public function clients()
    {
        $clients = User::where('is_admin', false)
            ->withCount(['appointments', 'messages'])
            ->with(['bookings' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
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

        // Get client's booking information including notes
        $bookings = Booking::where('email', $client->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.client-profile', compact('client', 'appointments', 'messages', 'bookings'));
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

        // Get bookings for the calendar
        $bookings = Booking::where('preferred_date', '>=', now()->subDays(7))
            ->where('preferred_date', '<=', now()->addDays(30))
            ->orderBy('preferred_date')
            ->orderBy('preferred_time')
            ->get();

        $clients = User::where('is_admin', false)->orderBy('name')->get();
        
        $todayAppointments = Appointment::where('appointment_date', today())->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $todayBookings = Booking::where('preferred_date', today())->count();

        return view('admin.calendar', compact('appointments', 'bookings', 'clients', 'todayAppointments', 'pendingAppointments', 'todayBookings'));
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
        
        // Get Stripe settings
        $stripeSettings = [
            'stripe_key' => \App\Models\Setting::get('stripe_key', config('services.stripe.key')),
            'stripe_secret' => \App\Models\Setting::get('stripe_secret', config('services.stripe.secret')),
            'stripe_webhook_secret' => \App\Models\Setting::get('stripe_webhook_secret', config('services.stripe.webhook_secret')),
        ];
        
        // Get SMTP settings
        $smtpSettings = [
            'mail_mailer' => \App\Models\Setting::get('mail_mailer', config('mail.default')),
            'mail_host' => \App\Models\Setting::get('mail_host', config('mail.mailers.smtp.host')),
            'mail_port' => \App\Models\Setting::get('mail_port', config('mail.mailers.smtp.port')),
            'mail_username' => \App\Models\Setting::get('mail_username', config('mail.mailers.smtp.username')),
            'mail_password' => \App\Models\Setting::get('mail_password', config('mail.mailers.smtp.password')),
            'mail_encryption' => \App\Models\Setting::get('mail_encryption', config('mail.mailers.smtp.encryption')),
            'mail_from_address' => \App\Models\Setting::get('mail_from_address', config('mail.from.address')),
            'mail_from_name' => \App\Models\Setting::get('mail_from_name', config('mail.from.name')),
        ];
        
        return view('admin.settings', compact('admin', 'stripeSettings', 'smtpSettings'));
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

    /**
     * Update Stripe settings
     */
    public function updateStripeSettings(Request $request)
    {
        $request->validate([
            'stripe_key' => 'nullable|string|max:255',
            'stripe_secret' => 'nullable|string|max:255',
            'stripe_webhook_secret' => 'nullable|string|max:255',
        ]);

        \App\Models\Setting::set('stripe_key', $request->stripe_key, 'stripe');
        \App\Models\Setting::set('stripe_secret', $request->stripe_secret, 'stripe');
        \App\Models\Setting::set('stripe_webhook_secret', $request->stripe_webhook_secret, 'stripe');

        // Clear config cache to ensure new settings are loaded
        \Artisan::call('config:clear');
        \Cache::flush();

        return redirect()->back()->with('success', 'Stripe settings updated successfully! Settings will take effect immediately.');
    }

    /**
     * Update SMTP settings
     */
    public function updateSmtpSettings(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string|max:50',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        \App\Models\Setting::set('mail_mailer', $request->mail_mailer, 'smtp');
        \App\Models\Setting::set('mail_host', $request->mail_host, 'smtp');
        \App\Models\Setting::set('mail_port', $request->mail_port, 'smtp');
        \App\Models\Setting::set('mail_username', $request->mail_username, 'smtp');
        \App\Models\Setting::set('mail_password', $request->mail_password, 'smtp');
        \App\Models\Setting::set('mail_encryption', $request->mail_encryption, 'smtp');
        \App\Models\Setting::set('mail_from_address', $request->mail_from_address, 'smtp');
        \App\Models\Setting::set('mail_from_name', $request->mail_from_name, 'smtp');

        // Clear config cache to ensure new settings are loaded
        \Artisan::call('config:clear');
        \Cache::flush();

        // Reload mail configuration immediately
        $this->reloadMailConfig();

        return redirect()->back()->with('success', 'SMTP settings updated successfully! Settings will take effect immediately.');
    }

    /**
     * Reload mail configuration from database
     */
    private function reloadMailConfig()
    {
        try {
            $mailMailer = \App\Models\Setting::get('mail_mailer');
            $mailHost = \App\Models\Setting::get('mail_host');
            $mailPort = \App\Models\Setting::get('mail_port');
            $mailUsername = \App\Models\Setting::get('mail_username');
            $mailPassword = \App\Models\Setting::get('mail_password');
            $mailEncryption = \App\Models\Setting::get('mail_encryption');
            $mailFromAddress = \App\Models\Setting::get('mail_from_address');
            $mailFromName = \App\Models\Setting::get('mail_from_name');

            if ($mailMailer) {
                \Config::set('mail.default', $mailMailer);
            }
            if ($mailHost) {
                \Config::set('mail.mailers.smtp.host', $mailHost);
            }
            if ($mailPort) {
                \Config::set('mail.mailers.smtp.port', $mailPort);
            }
            if ($mailUsername !== null) {
                \Config::set('mail.mailers.smtp.username', $mailUsername);
            }
            if ($mailPassword !== null) {
                \Config::set('mail.mailers.smtp.password', $mailPassword);
            }
            if ($mailEncryption !== null) {
                \Config::set('mail.mailers.smtp.encryption', $mailEncryption);
            }
            if ($mailFromAddress) {
                \Config::set('mail.from.address', $mailFromAddress);
            }
            if ($mailFromName) {
                \Config::set('mail.from.name', $mailFromName);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to reload mail config: ' . $e->getMessage());
        }
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
            $logoName = 'logo.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('public', $logoName);
            
            // Update logo path in config or database
            // You might want to store this in a settings table
                
                return redirect()->back()->with('success', 'Logo updated successfully!');
            }
            
        return redirect()->back()->with('error', 'No logo file uploaded.');
    }

    /**
     * Add a note for a client
     */
    public function addClientNote(Request $request, User $client)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $client->notes()->create([
            'admin_id' => Auth::id(),
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'Note added successfully!');
    }

    /**
     * Delete a client note
     */
    public function deleteClientNote(ClientNote $note)
    {
        // Check if the current user is the admin who created the note or a super admin
        if (Auth::id() !== $note->admin_id && !Auth::user()->is_admin) {
            abort(403, 'Access denied.');
        }

        $note->delete();
        return redirect()->back()->with('success', 'Note deleted successfully!');
    }

    public function appointments()
    {
        $query = Appointment::with('user');

        // Store filter preferences in session
        if (request('clear')) {
            // Clear stored filters
            session()->forget('appointment_filters');
        } elseif (request()->hasAny(['search', 'status'])) {
            session([
                'appointment_filters' => request()->only(['search', 'status'])
            ]);
        } else {
            // Use stored filters if no new filters are applied
            $storedFilters = session('appointment_filters', []);
            if (!empty($storedFilters)) {
                request()->merge($storedFilters);
            }
        }

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (request('status') && request('status') !== '') {
            $query->where('status', request('status'));
        }

        // Handle export request
        if (request('export')) {
            $appointments = $query->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();

            return $this->exportAppointments($appointments);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(20);

        return view('admin.appointments', compact('appointments'));
    }

    public function bookings()
    {
        // Show all bookings except completed/cancelled/converted ones
        $bookings = Booking::whereNotIn('status', ['completed', 'cancelled', 'converted'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings', compact('bookings'));
    }

    public function convertBookingToAppointment(Booking $booking)
    {
        // Find user based on email
        $user = User::where('email', $booking->email)->first();
        
        if (!$user) {
            return redirect()->back()->with('error', 'Cannot convert booking to appointment. User not found.');
        }
        
        // Check if user has signed agreement (either general or program-based)
        $hasGeneralAgreement = $user->hasSignedAgreement();
        $hasProgramAgreement = $user->userPrograms()
            ->whereNotNull('signed_agreement_path')
            ->exists();
            
        if (!$hasGeneralAgreement && !$hasProgramAgreement) {
            return redirect()->back()->with('error', 'Cannot convert booking to appointment. The signed agreement has not been uploaded yet.');
        }

        try {
            // Create appointment from booking
            $appointment = Appointment::create([
                'user_id' => $user->id,
                'program' => $booking->program ?? null,
                'appointment_date' => $booking->preferred_date,
                'appointment_time' => $booking->preferred_time,
                'booking_type' => $booking->booking_type ?? 'in-office',
                'message' => $booking->message,
                'status' => 'confirmed',
            ]);

            // Log the conversion for debugging
            \Log::info('Booking converted to appointment', [
                'booking_id' => $booking->id,
                'appointment_id' => $appointment->id,
                'user_id' => $user->id,
                'email' => $booking->email
            ]);

            // Delete the booking after successful conversion
            $booking->delete();

            return redirect()->route('admin.appointments')->with('success', 'Booking converted to appointment successfully! The appointment is now available in the appointments section.');
        } catch (\Exception $e) {
            \Log::error('Error converting booking to appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error converting booking to appointment: ' . $e->getMessage());
        }
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
            'admin_suggestion' => $request->message,
            'status' => 'suggested_alternative',
        ]);

        // Send email to client with suggested time
        // You can implement email notification here

        return redirect()->back()->with('success', 'Alternative time suggested to client! The booking is now marked as "Alternative Suggested" and will remain visible for further management.');
    }

    /**
     * Convert accepted booking to appointment
     */
    public function convertAcceptedBooking(Booking $booking)
    {
        // Verify the booking has been accepted by client
        if ($booking->status !== 'accepted') {
            return redirect()->back()->with('error', 'This booking has not been accepted by the client yet.');
        }

        // Find user based on email
        $user = User::where('email', $booking->email)->first();
        
        if (!$user) {
            return redirect()->back()->with('error', 'Cannot convert booking to appointment. User not found.');
        }
        
        // Check if user has signed agreement (either general or program-based)
        $hasGeneralAgreement = $user->hasSignedAgreement();
        $hasProgramAgreement = $user->userPrograms()
            ->whereNotNull('signed_agreement_path')
            ->exists();
            
        if (!$hasGeneralAgreement && !$hasProgramAgreement) {
            return redirect()->back()->with('error', 'Cannot convert booking to appointment. The signed agreement has not been uploaded yet.');
        }

        try {
            // Create appointment from accepted booking
            $appointment = Appointment::create([
                'user_id' => $user->id,
                'program' => $booking->program ?? null,
                'appointment_date' => $booking->preferred_date,
                'appointment_time' => $booking->preferred_time,
                'booking_type' => $booking->booking_type ?? 'in-office',
                'message' => $booking->message,
                'status' => 'confirmed',
            ]);

            // Delete the booking after successful conversion
            $booking->delete();

            // Log the conversion for debugging
            \Log::info('Accepted booking converted to appointment', [
                'booking_id' => $booking->id,
                'appointment_id' => $appointment->id,
                'user_id' => $user->id,
                'email' => $booking->email
            ]);

            return redirect()->route('admin.appointments')->with('success', 'Accepted booking converted to appointment successfully! The appointment is now available in the appointments section.');
        } catch (\Exception $e) {
            \Log::error('Error converting accepted booking to appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error converting accepted booking to appointment: ' . $e->getMessage());
        }
    }

    /**
     * Handle rejected booking (admin can suggest new time or cancel)
     */
    public function handleRejectedBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'action' => 'required|in:suggest_new_time,cancel',
            'new_suggested_date' => 'required_if:action,suggest_new_time|date|after:today',
            'new_suggested_time' => 'required_if:action,suggest_new_time|string',
            'admin_message' => 'nullable|string',
        ]);

        if ($request->action === 'suggest_new_time') {
            // Suggest a new alternative time
            $booking->update([
                'preferred_date' => $request->new_suggested_date,
                'preferred_time' => $request->new_suggested_time,
                'admin_suggestion' => $request->admin_message ?? 'We have suggested a new alternative time based on your feedback.',
                'status' => 'suggested_alternative',
                'client_response' => null, // Reset client response
                'response_date' => null,
            ]);

            return redirect()->back()->with('success', 'New alternative time suggested to client!');
        } else {
            // Cancel the booking
            $booking->update(['status' => 'cancelled']);
            return redirect()->back()->with('success', 'Booking cancelled due to client rejection.');
        }
    }

    /**
     * Handle modified booking (admin can accept modification or suggest alternative)
     */
    public function handleModifiedBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'action' => 'required|in:accept_modification,suggest_alternative',
            'suggested_date' => 'required_if:action,suggest_alternative|date|after:today',
            'suggested_time' => 'required_if:action,suggest_alternative|string',
            'admin_message' => 'nullable|string',
        ]);

        if ($request->action === 'accept_modification') {
            // Accept the client's modification
            $booking->update([
                'status' => 'accepted',
                'admin_suggestion' => 'We have accepted your requested modification.',
                'client_response' => $booking->client_response . ' (Accepted by admin)',
            ]);

            return redirect()->back()->with('success', 'Client modification accepted! The booking is now ready for conversion to appointment.');
        } else {
            // Suggest an alternative to the client's modification
            $booking->update([
                'preferred_date' => $request->suggested_date,
                'preferred_time' => $request->suggested_time,
                'admin_suggestion' => $request->admin_message ?? 'We have suggested an alternative time based on your modification request.',
                'status' => 'suggested_alternative',
                'client_response' => null, // Reset client response
                'response_date' => null,
            ]);

            return redirect()->back()->with('success', 'Alternative time suggested to client!');
        }
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

    /**
     * Export appointments to CSV
     */
    private function exportAppointments($appointments)
    {
        $filename = 'appointments_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($appointments) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Client Name',
                'Email',
                'Date',
                'Time',
                'Status',
                'Program',
                'Notes',
                'Created At'
            ]);

            // Add data rows
            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->user->name ?? 'N/A',
                    $appointment->user->email ?? 'N/A',
                    $appointment->appointment_date->format('Y-m-d'),
                    $appointment->formatted_time,
                    ucfirst($appointment->status),
                    $appointment->program_name,
                    $appointment->message ?? 'N/A',
                    $appointment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show all payments and subscriptions
     */
    public function payments()
    {
        // Get all payments with related data
        $payments = Payment::with([
            'userProgram.user', 
            'userProgram.program', 
            'userProgram.payments',
            'appointment'
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get payment statistics
        $totalRevenue = Payment::where('status', Payment::STATUS_COMPLETED)->sum('amount');
        $pendingPayments = Payment::where('status', Payment::STATUS_PENDING)->count();
        $completedPayments = Payment::where('status', Payment::STATUS_COMPLETED)->count();
        $failedPayments = Payment::where('status', Payment::STATUS_FAILED)->count();
        
        // Get payment breakdown by type
        $paymentsByType = [
            'contract_monthly' => Payment::where('payment_type', Payment::TYPE_CONTRACT_MONTHLY)
                ->where('status', Payment::STATUS_COMPLETED)
                ->sum('amount'),
            'contract_one_time' => Payment::where('payment_type', Payment::TYPE_CONTRACT_ONE_TIME)
                ->where('status', Payment::STATUS_COMPLETED)
                ->sum('amount'),
            'additional_session' => Payment::where('payment_type', Payment::TYPE_ADDITIONAL_SESSION)
                ->where('status', Payment::STATUS_COMPLETED)
                ->sum('amount'),
        ];
        
        return view('admin.payments', compact(
            'payments',
            'totalRevenue',
            'pendingPayments',
            'completedPayments',
            'failedPayments',
            'paymentsByType'
        ));
    }

    /**
     * Show all subscriptions (UserPrograms)
     */
    public function subscriptions()
    {
        // Get all user programs (subscriptions) with related data
        $subscriptions = UserProgram::with(['user', 'program', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get subscription statistics
        $activeSubscriptions = UserProgram::where('status', UserProgram::STATUS_ACTIVE)->count();
        $monthlySubscriptions = UserProgram::where('payment_type', UserProgram::PAYMENT_TYPE_MONTHLY)
            ->where('status', UserProgram::STATUS_ACTIVE)
            ->count();
        $oneTimeSubscriptions = UserProgram::where('payment_type', UserProgram::PAYMENT_TYPE_ONE_TIME)
            ->where('status', UserProgram::STATUS_ACTIVE)
            ->count();
        
        // Get subscriptions by status
        $subscriptionsByStatus = [
            'active' => UserProgram::where('status', UserProgram::STATUS_ACTIVE)->count(),
            'approved' => UserProgram::where('status', UserProgram::STATUS_APPROVED)->count(),
            'cancelled' => UserProgram::where('status', UserProgram::STATUS_CANCELLED)->count(),
            'rejected' => UserProgram::where('status', UserProgram::STATUS_REJECTED)->count(),
        ];
        
        return view('admin.subscriptions-list', compact(
            'subscriptions',
            'activeSubscriptions',
            'monthlySubscriptions',
            'oneTimeSubscriptions',
            'subscriptionsByStatus'
        ));
    }
} 