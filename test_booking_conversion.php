<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Booking;
use App\Models\Appointment;

echo "=== Booking Conversion Test ===\n";

// Create a test user with signed agreement
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
    'signed_agreement_path' => 'test-agreement.pdf',
    'is_admin' => false
]);

echo "Created user: {$user->id}\n";
echo "User has agreement: " . ($user->hasSignedAgreement() ? 'Yes' : 'No') . "\n";

// Create a test booking
$booking = Booking::create([
    'full_name' => 'Test User',
    'email' => 'test@example.com',
    'preferred_date' => now()->addDays(7),
    'preferred_time' => '10:00',
    'message' => 'Test booking',
    'status' => 'pending'
]);

echo "Created booking: {$booking->id}\n";

// Test the conversion logic
try {
    // Find user based on email
    $foundUser = User::where('email', $booking->email)->first();
    
    if (!$foundUser) {
        echo "ERROR: User not found for email: {$booking->email}\n";
        exit(1);
    }
    
    if (!$foundUser->hasSignedAgreement()) {
        echo "ERROR: User does not have signed agreement\n";
        exit(1);
    }
    
    // Create appointment from booking
    $appointment = Appointment::create([
        'user_id' => $foundUser->id,
        'program' => $booking->program ?? null,
        'appointment_date' => $booking->preferred_date,
        'appointment_time' => $booking->preferred_time,
        'message' => $booking->message,
        'status' => 'confirmed',
    ]);
    
    echo "SUCCESS: Created appointment: {$appointment->id}\n";
    echo "Appointment date: {$appointment->appointment_date}\n";
    echo "Appointment time: {$appointment->appointment_time}\n";
    echo "Appointment status: {$appointment->status}\n";
    
    // Clean up
    $appointment->delete();
    $booking->delete();
    $user->delete();
    
    echo "Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}










