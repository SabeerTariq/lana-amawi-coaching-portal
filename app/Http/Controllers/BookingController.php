<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Mail\ClientCredentials;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function index()
    {
        return view('booking');
    }

    public function downloadAgreement(Request $request)
    {
        // Validate the booking data
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Store booking data in session for later use
        session([
            'pending_booking' => $request->all()
        ]);

        // Generate agreement PDF
        $pdf = PDF::loadView('agreements.coaching_agreement', [
            'client_name' => $request->full_name,
            'client_email' => $request->email,
            'client_phone' => $request->phone,
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $request->preferred_time,
            'message' => $request->message,
            'agreement_date' => now()->format('F j, Y'),
        ]);

        return $pdf->download('coaching_agreement_' . Str::slug($request->full_name) . '.pdf');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string',
            'message' => 'nullable|string|max:1000',
            'terms' => 'required|accepted',
            'signed_agreement' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            \Log::info('Starting booking process with signed agreement for email: ' . $request->email);
            
            // Handle file upload
            $signedAgreementPath = $request->file('signed_agreement')->store('agreements/signed', 'public');
            $signedAgreementName = $request->file('signed_agreement')->getClientOriginalName();
            
            // Check if user already exists
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                // Generate a random password
                $password = Str::random(8);
                
                // Create new user with agreement
                $user = User::create([
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'password' => Hash::make($password),
                    'is_admin' => false,
                    'signed_agreement_path' => $signedAgreementPath,
                    'signed_agreement_name' => $signedAgreementName,
                    'agreement_uploaded_at' => now(),
                ]);
                
                // Send credentials email
                try {
                    Mail::to($user->email)->send(new ClientCredentials($user, $password));
                } catch (\Exception $mailException) {
                    \Log::error('Mail sending failed: ' . $mailException->getMessage());
                    // Continue with booking even if email fails
                }
            } else {
                // User already exists, update their agreement
                $user->update([
                    'signed_agreement_path' => $signedAgreementPath,
                    'signed_agreement_name' => $signedAgreementName,
                    'agreement_uploaded_at' => now(),
                ]);
                
                // Generate a new password and update their account
                $newPassword = Str::random(8);
                $user->update([
                    'password' => Hash::make($newPassword)
                ]);
                
                // Send new credentials email
                try {
                    Mail::to($user->email)->send(new ClientCredentials($user, $newPassword));
                } catch (\Exception $mailException) {
                    \Log::error('Mail sending failed for existing user: ' . $mailException->getMessage());
                    // Continue with booking even if email fails
                }
            }

            $booking = Booking::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'program' => null, // Program field removed from form
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            // Clear the pending booking session data
            session()->forget('pending_booking');

            \Log::info('Booking with signed agreement completed successfully for user: ' . $user->email);
            
            // Store email in session for pre-filling login form
            session(['booking_email' => $user->email]);
            
            // Redirect to client login with success messages
            return redirect()->route('client.login')
                ->with('success', 'Your booking has been submitted successfully!')
                ->with('email_check', 'Please check your email for your portal login credentials (email and password).');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Booking submission error: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while submitting your booking. Please try again. Error: ' . $e->getMessage()])
                ->withInput();
        }
    }
} 