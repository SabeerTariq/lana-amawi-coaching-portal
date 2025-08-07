<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Message;

class ClientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $nextAppointment = Appointment::where('user_id', $user->id)
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
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
            ->where('status', 'completed')
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
        
        $upcomingAppointments = Appointment::where('user_id', $user->id)
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $pastAppointments = Appointment::where('user_id', $user->id)
            ->where('appointment_date', '<', now()->toDateString())
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('client.appointments', compact('upcomingAppointments', 'pastAppointments'));
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
            ->orderBy('created_at', 'desc')
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
} 