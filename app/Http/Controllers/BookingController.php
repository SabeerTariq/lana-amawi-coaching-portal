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

        // Use the static PDF template instead of generating dynamic content
        $templatePath = 'agreement-templates/life-coaching-contract.pdf';
        $templateFullPath = storage_path('app/public/' . $templatePath);
        
        // Check if template exists
        if (!file_exists($templateFullPath)) {
            return response()->json(['error' => 'Agreement template not found.'], 404);
        }

        // Return the static PDF for download
        return response()->download($templateFullPath, 'life_coaching_contract.pdf');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other,prefer_not_to_say',
            'age' => 'required|integer|min:18|max:100',
            'languages_spoken' => 'required|array|min:1',
            'languages_spoken.*' => 'string|in:English,Arabic,French,Spanish,Mandarin Chinese,German,Japanese,Vietnamese,Other',
            'institution_hospital' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'position_as_of_date' => 'required|date|before_or_equal:today',
            'specialty' => 'required|string|max:255',
            'education_institution' => 'required|string|max:255',
            'graduation_date' => 'nullable|date|before_or_equal:today',
            'phone' => 'required|string|max:20',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            \Log::info('Starting registration process for email: ' . $request->email);
            
            // Check if user already exists
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                // User already exists - return error message
                \Log::info('Registration attempted with existing email: ' . $request->email);
                return redirect()->back()
                    ->withErrors(['email' => 'An account with this email address already exists. Please login instead.'])
                    ->withInput();
            }
            
            // Generate a random password
            $password = Str::random(8);
            
            // Create new user with professional information
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'age' => $request->age,
                'languages_spoken' => $request->languages_spoken,
                'institution_hospital' => $request->institution_hospital,
                'position' => $request->position,
                'position_as_of_date' => $request->position_as_of_date,
                'specialty' => $request->specialty,
                'education_institution' => $request->education_institution,
                'graduation_date' => $request->graduation_date,
                'password' => Hash::make($password),
                'is_admin' => false,
            ]);
            
            // Send credentials email
            try {
                Mail::to($user->email)->send(new ClientCredentials($user, $password));
            } catch (\Exception $mailException) {
                \Log::error('Mail sending failed: ' . $mailException->getMessage());
                // Continue with registration even if email fails
            }

            \Log::info('User registration completed successfully for user: ' . $user->email);
            
            // Store email in session for pre-filling login form
            session(['registration_email' => $user->email]);
            
            // Redirect to client login with success messages
            return redirect()->route('client.login')
                ->with('success', 'Your professional registration has been completed successfully!')
                ->with('email_check', 'Please check your email for your portal login credentials.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Registration submission error: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while submitting your registration. Please try again. Error: ' . $e->getMessage()])
                ->withInput();
        }
    }
} 