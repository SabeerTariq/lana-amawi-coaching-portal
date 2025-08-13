<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [BookingController::class, 'index'])->name('booking');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

// Authentication routes
// Separate admin and client login routes
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::get('/client/login', [AuthController::class, 'showClientLogin'])->name('client.login');
Route::post('/client/login', [AuthController::class, 'clientLogin']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Client routes (authenticated)
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [ClientController::class, 'appointments'])->name('appointments');
    Route::get('/messages', [ClientController::class, 'messages'])->name('messages');
    Route::post('/messages/send', [ClientController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/attachment/{message}', [ClientController::class, 'downloadAttachment'])->name('messages.attachment');
    Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
    
    // Appointment management
    Route::put('/appointments/{appointment}/reschedule', [ClientController::class, 'rescheduleAppointment'])->name('appointments.reschedule');
    Route::delete('/appointments/{appointment}/cancel', [ClientController::class, 'cancelAppointment'])->name('appointments.cancel');
    
    // Booking new sessions
    Route::post('/book-session', [ClientController::class, 'bookNewSession'])->name('book-session');

    // Respond to suggested alternative times
    Route::post('/bookings/{booking}/accept', [ClientController::class, 'acceptSuggestedTime'])->name('bookings.accept');
    Route::post('/bookings/{booking}/reject', [ClientController::class, 'rejectSuggestedTime'])->name('bookings.reject');
    Route::post('/bookings/{booking}/modify', [ClientController::class, 'modifySuggestedTime'])->name('bookings.modify');
});

// Admin routes (authenticated + admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/clients', [AdminController::class, 'clients'])->name('clients');
    Route::get('/clients/{client}', [AdminController::class, 'clientProfile'])->name('clients.profile');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::post('/messages/send', [AdminController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/attachment/{message}', [AdminController::class, 'downloadAttachment'])->name('messages.attachment');
    Route::get('/calendar', [AdminController::class, 'calendar'])->name('calendar');
    Route::get('/calendar-test', function() {
        return view('admin.calendar-test');
    })->name('calendar.test');
    
    // Simple test route for calendar debugging
    Route::get('/calendar-debug', function() {
        return response()->json([
            'fullcalendar_loaded' => true,
            'test_data' => [
                [
                    'id' => 'test1',
                    'title' => 'Test Appointment 1',
                    'start' => now()->toISOString(),
                    'backgroundColor' => '#007bff'
                ],
                [
                    'id' => 'test2', 
                    'title' => 'Test Appointment 2',
                    'start' => now()->addDay()->toISOString(),
                    'backgroundColor' => '#28a745'
                ]
            ]
        ]);
    })->name('calendar.debug');
    Route::get('/calendar/export', [AdminController::class, 'exportCalendar'])->name('calendar.export');
    Route::get('/appointments/{id}', [AdminController::class, 'getAppointment'])->name('appointments.show');
    Route::post('/appointments', [AdminController::class, 'storeAppointment'])->name('appointments.store');
    Route::put('/appointments/{appointment}/reschedule', [AdminController::class, 'rescheduleAppointment'])->name('appointments.reschedule');
    Route::delete('/appointments/{appointment}/cancel', [AdminController::class, 'cancelAppointment'])->name('appointments.cancel');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    
    // Appointment management
    Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
    Route::put('/appointments/{appointment}/confirm', [AdminController::class, 'confirmAppointment'])->name('appointments.confirm');
    Route::put('/appointments/{appointment}/complete', [AdminController::class, 'completeAppointment'])->name('appointments.complete');
    
    // Booking management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{booking}/convert', [AdminController::class, 'convertBookingToAppointment'])->name('bookings.convert');
    Route::post('/bookings/{booking}/suggest-time', [AdminController::class, 'suggestAlternativeTime'])->name('bookings.suggest-time');
    
    // Handle client responses to suggested times
    Route::post('/bookings/{booking}/convert-accepted', [AdminController::class, 'convertAcceptedBooking'])->name('bookings.convert-accepted');
    Route::post('/bookings/{booking}/handle-rejection', [AdminController::class, 'handleRejectedBooking'])->name('bookings.handle-rejection');
    Route::post('/bookings/{booking}/handle-modification', [AdminController::class, 'handleModifiedBooking'])->name('bookings.handle-modification');
});

// Test route for debugging booking
Route::get('/test-booking', function() {
    try {
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_admin' => false,
        ]);
        
        $booking = \App\Models\Booking::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'program' => 'life_coaching',
            'preferred_date' => '2025-08-10',
            'preferred_time' => '09:00',
            'message' => 'Test booking',
            'status' => 'pending',
        ]);
        
        return 'Test successful! User ID: ' . $user->id . ', Booking ID: ' . $booking->id;
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Test email route
Route::get('/test-email', function() {
    try {
        Mail::raw('Test email from Lana Amawi Coaching Portal', function($message) {
            $message->to('jadipe4973@percyfx.com')
                   ->subject('Test Email - Lana Amawi Coaching');
        });
        return 'Test email sent successfully! Check your Mailtrap inbox.';
    } catch (\Exception $e) {
        return 'Email error: ' . $e->getMessage();
    }
});

// Test ClientCredentials email
Route::get('/test-credentials-email', function() {
    try {
        $user = \App\Models\User::where('email', 'jadipe4973@percyfx.com')->first();
        if ($user) {
            Mail::to($user->email)->send(new \App\Mail\ClientCredentials($user, 'TestPassword123'));
            return 'ClientCredentials email sent successfully! Check your Mailtrap inbox.';
        } else {
            return 'User not found.';
        }
    } catch (\Exception $e) {
        return 'Email error: ' . $e->getMessage();
    }
});
