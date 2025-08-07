<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Mail\ClientCredentials;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        return view('booking');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'program' => 'required|string|in:life_coaching,career_coaching,relationship_coaching,wellness_coaching',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string',
            'message' => 'nullable|string|max:1000',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            \Log::info('Starting booking process for email: ' . $request->email);
            
            // Check if user already exists
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                // Generate a random password
                $password = Str::random(8);
                
                // Create new user
                $user = User::create([
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'password' => Hash::make($password),
                    'is_admin' => false,
                ]);
                
                // Send credentials email
                try {
                    Mail::to($user->email)->send(new ClientCredentials($user, $password));
                } catch (\Exception $mailException) {
                    \Log::error('Mail sending failed: ' . $mailException->getMessage());
                    // Continue with booking even if email fails
                }
            }

            $booking = Booking::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'program' => $request->program,
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            // Send credentials email if user was just created
            if ($user->wasRecentlyCreated) {
                // User was just created, credentials email already sent above
            } else {
                // User already exists, generate a new password and update their account
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

            \Log::info('Booking completed successfully for user: ' . $user->email);
            return redirect()->back()->with('success', 'Your booking has been submitted successfully! Please check your email for portal login credentials.');
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