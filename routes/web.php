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
Route::post('/booking/agreement/download', [BookingController::class, 'downloadAgreement'])->name('booking.agreement.download');
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
    
    // Program management
    Route::get('/programs', [App\Http\Controllers\ProgramController::class, 'index'])->name('programs');
    Route::get('/programs/{program}', [App\Http\Controllers\ProgramController::class, 'show'])->name('programs.show');
    Route::post('/programs/select', [App\Http\Controllers\ProgramController::class, 'selectProgram'])->name('programs.select');
    Route::get('/programs/agreement/{userProgram}/download', [App\Http\Controllers\ProgramController::class, 'downloadAgreement'])->name('programs.agreement.download');
    Route::post('/programs/agreement/{userProgram}/upload', [App\Http\Controllers\ProgramController::class, 'uploadSignedAgreement'])->name('programs.agreement.upload');
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
    Route::post('/settings/logo', [AdminController::class, 'updateLogo'])->name('settings.logo');
    
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

    // Enhanced Slot management
    Route::get('/enhanced-slot-management', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'index'])->name('enhanced-slot-management');
    Route::get('/slot-management/schedules', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'schedules'])->name('slot-management.schedules');
    Route::get('/slot-management/exceptions', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'exceptions'])->name('slot-management.exceptions');
    
    // Schedule CRUD
    Route::post('/slot-management/schedules', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'storeSchedule'])->name('slot-management.schedules.store');
    Route::put('/slot-management/schedules/{schedule}', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'updateSchedule'])->name('slot-management.schedules.update');
    Route::delete('/slot-management/schedules/{schedule}', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'deleteSchedule'])->name('slot-management.schedules.delete');
    Route::post('/slot-management/schedules/bulk', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'bulkScheduleOperation'])->name('slot-management.schedules.bulk');
    
    // Exception CRUD
    Route::post('/slot-management/exceptions', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'storeException'])->name('slot-management.exceptions.store');
    Route::put('/slot-management/exceptions/{exception}', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'updateException'])->name('slot-management.exceptions.update');
    Route::delete('/slot-management/exceptions/{exception}', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'deleteException'])->name('slot-management.exceptions.delete');
    
    // API endpoints
    Route::post('/slot-management/availability', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'getAvailability'])->name('slot-management.availability');
    Route::post('/slot-management/check-slot', [App\Http\Controllers\Admin\EnhancedSlotManagementController::class, 'checkSlot'])->name('slot-management.check-slot');
    
    // Original slot management (keep for compatibility)
    Route::get('/slot-management', [App\Http\Controllers\Admin\SlotManagementController::class, 'index'])->name('slot-management');
    
    // Client notes management
    Route::post('/clients/{client}/notes', [AdminController::class, 'addClientNote'])->name('clients.notes.add');
    Route::delete('/clients/notes/{note}', [AdminController::class, 'deleteClientNote'])->name('clients.notes.delete');
    
    // Program management
    Route::get('/programs/applications', [App\Http\Controllers\Admin\ProgramController::class, 'applications'])->name('programs.applications');
    Route::get('/programs/{program}/applications', [App\Http\Controllers\Admin\ProgramController::class, 'programApplications'])->name('programs.program-applications');
    Route::post('/programs/{userProgram}/send-agreement', [App\Http\Controllers\Admin\ProgramController::class, 'sendAgreement'])->name('programs.send-agreement');
    Route::get('/programs/{userProgram}/view-agreement', [App\Http\Controllers\Admin\ProgramController::class, 'viewAgreement'])->name('programs.view-agreement');
    Route::post('/programs/{userProgram}/approve', [App\Http\Controllers\Admin\ProgramController::class, 'approveApplication'])->name('programs.approve');
    Route::post('/programs/{userProgram}/request-payment', [App\Http\Controllers\Admin\ProgramController::class, 'requestPayment'])->name('programs.request-payment');
    Route::post('/programs/{userProgram}/mark-payment-completed', [App\Http\Controllers\Admin\ProgramController::class, 'markPaymentCompleted'])->name('programs.mark-payment-completed');
    Route::post('/programs/{userProgram}/activate', [App\Http\Controllers\Admin\ProgramController::class, 'activateProgram'])->name('programs.activate');
    Route::post('/programs/{userProgram}/reject', [App\Http\Controllers\Admin\ProgramController::class, 'rejectApplication'])->name('programs.reject');
    Route::post('/programs/{userProgram}/add-notes', [App\Http\Controllers\Admin\ProgramController::class, 'addNotes'])->name('programs.add-notes');
    
    // Program CRUD management
    Route::resource('programs', App\Http\Controllers\Admin\ProgramController::class)->except(['show']);
    Route::post('/programs/{program}/toggle-status', [App\Http\Controllers\Admin\ProgramController::class, 'toggleStatus'])->name('programs.toggle-status');
    
    // Subscription management
    Route::resource('subscriptions', App\Http\Controllers\Admin\SubscriptionController::class);
    Route::post('/subscriptions/{subscription}/toggle-status', [App\Http\Controllers\Admin\SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle-status');
    Route::post('/subscriptions/{subscription}/reset-monthly', [App\Http\Controllers\Admin\SubscriptionController::class, 'resetMonthlyCount'])->name('subscriptions.reset-monthly');
    Route::post('/subscriptions/{subscription}/extend', [App\Http\Controllers\Admin\SubscriptionController::class, 'extend'])->name('subscriptions.extend');
    
    // Client profile
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

// API Routes for slot management
Route::prefix('api')->group(function () {
    Route::get('/available-slots', [App\Http\Controllers\Api\SlotController::class, 'getAvailableSlots']);
    Route::get('/available-booking-types', [App\Http\Controllers\Api\SlotController::class, 'getAvailableBookingTypes']);
    Route::get('/schedule', [App\Http\Controllers\Api\SlotController::class, 'getSchedule']);
    Route::post('/check-slot', [App\Http\Controllers\Api\SlotController::class, 'checkSlotAvailability']);
});
