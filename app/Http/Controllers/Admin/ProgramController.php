<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\UserProgram;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProgramAgreementSent;

class ProgramController extends Controller
{
    /**
     * Display all program applications
     */
    public function applications()
    {
        // Only show applications from agreement_uploaded status onwards
        // Admin doesn't need to see pending, agreement_sent, or payment_requested statuses
        $allApplications = UserProgram::with(['user', 'program'])
            ->whereIn('status', [
                UserProgram::STATUS_AGREEMENT_UPLOADED,
                UserProgram::STATUS_APPROVED,
                UserProgram::STATUS_ACTIVE,
                UserProgram::STATUS_REJECTED,
                UserProgram::STATUS_CANCELLED
            ])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $applications = $allApplications->groupBy('status');

        return view('admin.programs.applications', compact('applications'));
    }

    /**
     * Display applications for a specific program
     */
    public function programApplications(Program $program)
    {
        $applications = UserProgram::where('program_id', $program->id)
            ->with(['user', 'program'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');

        return view('admin.programs.program-applications', compact('applications', 'program'));
    }

    /**
     * Send agreement to client
     */
public function sendAgreement(UserProgram $userProgram)
    {
        // Get program-specific agreement template or fallback to default
        $templatePath = $userProgram->program->agreement_template_path ?? 'agreement-templates/life-coaching-contract.pdf';
        $templateFullPath = storage_path('app/public/' . $templatePath);
        
        // Check if template exists
        if (!file_exists($templateFullPath)) {
            return redirect()->back()->with('error', 'Agreement template not found for this program. Please upload an agreement template in program settings.');
        }

        // Copy the template to agreements folder with unique name
        $fileName = 'agreement_' . $userProgram->id . '_' . time() . '.pdf';
        $filePath = 'agreements/' . $fileName;
        
        // Copy the template file
        Storage::disk('public')->copy($templatePath, $filePath);

        // Update user program
        $userProgram->update([
            'agreement_path' => $filePath,
            'agreement_sent_at' => now(),
            'status' => UserProgram::STATUS_AGREEMENT_SENT,
        ]);

        // Send email notification
        try {
            Mail::to($userProgram->user->email)->send(new ProgramAgreementSent($userProgram));
        } catch (\Exception $e) {
            \Log::error('Failed to send agreement email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Agreement sent to ' . $userProgram->user->name . ' successfully!');
    }

    /**
     * View uploaded agreement
     */
    public function viewAgreement(UserProgram $userProgram)
    {
        if (!$userProgram->hasSignedAgreement()) {
            return redirect()->back()->with('error', 'No signed agreement found.');
        }

        $filePath = storage_path('app/public/' . $userProgram->signed_agreement_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Agreement file not found.');
        }

        return response()->file($filePath);
    }

    /**
     * Approve program application
     */
    public function approveApplication(UserProgram $userProgram)
    {
        $userProgram->update([
            'status' => UserProgram::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Program application approved for ' . $userProgram->user->name . '!');
    }

    /**
     * Request payment
     */
    public function requestPayment(Request $request, UserProgram $userProgram)
    {
        $request->validate([
            'payment_type' => 'required|in:monthly,one_time',
        ]);

        // Set default contract duration if not set
        if (!$userProgram->contract_duration_months) {
            $userProgram->update(['contract_duration_months' => 3]);
        }

        $userProgram->update([
            'status' => UserProgram::STATUS_PAYMENT_REQUESTED,
            'payment_requested_at' => now(),
            'payment_type' => $request->payment_type,
        ]);

        $paymentTypeText = $request->payment_type === 'monthly' ? 'monthly payments' : 'one-time payment';
        return redirect()->back()->with('success', 'Payment requested (' . $paymentTypeText . ') for ' . $userProgram->user->name . '!');
    }

    /**
     * Mark payment as completed
     */
    public function markPaymentCompleted(Request $request, UserProgram $userProgram)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_reference' => 'required|string|max:255',
            'payment_type' => 'nullable|in:contract_monthly,contract_one_time,additional_session',
            'month_number' => 'nullable|integer|min:1|max:3',
        ]);

        $paymentType = $request->payment_type ?? ($userProgram->payment_type === 'one_time' ? Payment::TYPE_CONTRACT_ONE_TIME : Payment::TYPE_CONTRACT_MONTHLY);
        $monthNumber = $request->month_number ?? ($userProgram->payments_completed + 1);

        // Create payment record
        $payment = Payment::create([
            'user_program_id' => $userProgram->id,
            'payment_type' => $paymentType,
            'status' => Payment::STATUS_COMPLETED,
            'amount' => $request->amount_paid,
            'payment_reference' => $request->payment_reference,
            'month_number' => $paymentType === Payment::TYPE_CONTRACT_MONTHLY ? $monthNumber : null,
            'paid_at' => now(),
        ]);

        // Update user program
        $updateData = [
            'payment_completed_at' => now(),
            'amount_paid' => $request->amount_paid,
            'payment_reference' => $request->payment_reference,
            'payments_completed' => $userProgram->payments_completed + 1,
        ];

        // If monthly payment, update next payment date
        if ($paymentType === Payment::TYPE_CONTRACT_MONTHLY && $userProgram->payment_type === UserProgram::PAYMENT_TYPE_MONTHLY) {
            $updateData['next_payment_date'] = now()->addMonth();
        }

        // If one-time payment or all payments completed, mark as payment completed
        if ($paymentType === Payment::TYPE_CONTRACT_ONE_TIME || 
            ($userProgram->payments_completed + 1 >= $userProgram->total_payments_due)) {
            $updateData['status'] = UserProgram::STATUS_PAYMENT_COMPLETED;
        }

        $userProgram->update($updateData);

        return redirect()->back()->with('success', 'Payment marked as completed for ' . $userProgram->user->name . '!');
    }

    /**
     * Activate program
     */
    public function activateProgram(UserProgram $userProgram)
    {
        // Initialize contract if not already initialized
        if (!$userProgram->contract_start_date) {
            $paymentType = $userProgram->payment_type ?? UserProgram::PAYMENT_TYPE_MONTHLY;
            $userProgram->initializeContract($paymentType);
        }

        $userProgram->update([
            'status' => UserProgram::STATUS_ACTIVE,
        ]);

        return redirect()->back()->with('success', 'Program activated for ' . $userProgram->user->name . '! Contract initialized.');
    }

    /**
     * Mark additional session payment as completed
     */
    public function markAdditionalSessionPayment(Request $request, UserProgram $userProgram)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_reference' => 'required|string|max:255',
        ]);

        // Create payment record for additional session
        Payment::create([
            'user_program_id' => $userProgram->id,
            'appointment_id' => $request->appointment_id,
            'payment_type' => Payment::TYPE_ADDITIONAL_SESSION,
            'status' => Payment::STATUS_COMPLETED,
            'amount' => $request->amount_paid,
            'payment_reference' => $request->payment_reference,
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Additional session payment marked as completed!');
    }

    /**
     * Reject application
     */
    public function rejectApplication(Request $request, UserProgram $userProgram)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        // Append rejection note to existing admin notes
        $adminNotes = $userProgram->admin_notes ?? '';
        if (!empty($adminNotes)) {
            $adminNotes .= "\n\n";
        }
        $adminNotes .= "[REJECTED]\n";
        $adminNotes .= "Reason: " . $request->admin_notes . "\n";
        $adminNotes .= "Rejected by: " . auth()->user()->name . " (ID: " . auth()->id() . ")\n";
        $adminNotes .= "Rejected at: " . now()->format('Y-m-d H:i:s');

        $userProgram->update([
            'status' => UserProgram::STATUS_REJECTED,
            'admin_notes' => $adminNotes,
        ]);

        return redirect()->back()->with('success', 'Application rejected for ' . $userProgram->user->name . '.');
    }

    /**
     * Add admin notes
     */
    public function addNotes(Request $request, UserProgram $userProgram)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $userProgram->update([
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->back()->with('success', 'Notes added successfully.');
    }

    /**
     * Display a listing of programs
     */
    public function index()
    {
        // Get programs with correct counts
        // Applications: All UserProgram records (excluding cancelled)
        // Subscriptions: Active UserProgram records (status = 'active')
        $programs = Program::withCount([
            'userPrograms as applications_count' => function($query) {
                $query->where('status', '!=', UserProgram::STATUS_CANCELLED);
            },
            'userPrograms as subscriptions_count' => function($query) {
                $query->where('status', UserProgram::STATUS_ACTIVE);
            }
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        // Map the counts to match the view expectations
        $programs->each(function($program) {
            // Applications count (all non-cancelled user programs)
            $program->user_programs_count = $program->applications_count ?? 0;
            // Subscriptions count (active user programs)
            $program->subscriptions_count = $program->subscriptions_count ?? 0;
        });

        return view('admin.programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new program
     */
    public function create()
    {
        return view('admin.programs.create');
    }

    /**
     * Store a newly created program
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'subscription_type' => 'nullable|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'monthly_sessions' => 'required|integer|min:1',
            'additional_booking_charge' => 'nullable|numeric|min:0',
            'one_time_payment_amount' => 'nullable|numeric|min:0',
            'agreement_template' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
        ]);

        // Prepare data with proper checkbox handling
        $data = $request->all();
        $data['is_subscription_based'] = 1; // Always subscription-based
        $data['is_active'] = $request->has('is_active') ? 1 : 1; // Default to active if not specified
        $data['price'] = 0; // Set price to 0 since we only use monthly subscriptions

        // Handle agreement template upload
        if ($request->hasFile('agreement_template')) {
            $file = $request->file('agreement_template');
            $fileName = 'agreement_' . Str::slug($request->name) . '_' . time() . '.pdf';
            $filePath = $file->storeAs('agreement-templates', $fileName, 'public');
            $data['agreement_template_path'] = $filePath;
        }

        // Filter out empty feature strings
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_filter($data['features'], function($feature) {
                return !empty(trim($feature));
            });
            $data['features'] = array_values($data['features']); // Re-index array
        }

        // Remove file from data array (not a database field)
        unset($data['agreement_template']);

        $program = Program::create($data);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program created successfully!');
    }

    /**
     * Display the specified program
     */
    public function show(Program $program)
    {
        $program->load(['userPrograms.user', 'subscriptions.user']);
        
        return view('admin.programs.show', compact('program'));
    }

    /**
     * Show the form for editing the program
     */
    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    /**
     * Update the specified program
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'subscription_type' => 'nullable|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'monthly_sessions' => 'required|integer|min:1',
            'additional_booking_charge' => 'nullable|numeric|min:0',
            'one_time_payment_amount' => 'nullable|numeric|min:0',
            'agreement_template' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
        ]);

        // Prepare data with proper checkbox handling
        $data = $request->all();
        $data['is_subscription_based'] = 1; // Always subscription-based
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['price'] = 0; // Set price to 0 since we only use monthly subscriptions

        // Handle agreement template upload
        if ($request->hasFile('agreement_template')) {
            // Delete old agreement if exists
            if ($program->agreement_template_path && Storage::disk('public')->exists($program->agreement_template_path)) {
                Storage::disk('public')->delete($program->agreement_template_path);
            }
            
            $file = $request->file('agreement_template');
            $fileName = 'agreement_' . Str::slug($request->name) . '_' . time() . '.pdf';
            $filePath = $file->storeAs('agreement-templates', $fileName, 'public');
            $data['agreement_template_path'] = $filePath;
        }

        // Filter out empty feature strings
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_filter($data['features'], function($feature) {
                return !empty(trim($feature));
            });
            $data['features'] = array_values($data['features']); // Re-index array
        }

        // Remove file from data array (not a database field)
        unset($data['agreement_template']);

        $program->update($data);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program updated successfully!');
    }

    /**
     * Remove the specified program
     */
    public function destroy(Program $program)
    {
        // Check if program has active subscriptions
        if ($program->activeSubscriptions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete program with active subscriptions. Please deactivate subscriptions first.');
        }

        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program deleted successfully!');
    }

    /**
     * Toggle program active status
     */
    public function toggleStatus(Program $program)
    {
        $program->update(['is_active' => !$program->is_active]);

        $status = $program->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Program {$status} successfully!");
    }

    /**
     * Admin cancels a user program/subscription
     */
    public function cancelSubscription(Request $request, UserProgram $userProgram)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        // Check if program can be cancelled
        if (!$userProgram->canBeCancelled()) {
            return redirect()->back()->with('error', 'This program cannot be cancelled.');
        }

        try {
            // Cancel Stripe subscription if it's a monthly subscription
            if ($userProgram->payment_type === UserProgram::PAYMENT_TYPE_MONTHLY && $userProgram->stripe_subscription_id) {
                try {
                    $stripeService = new \App\Services\StripeService();
                    $stripeService->cancelSubscription($userProgram->stripe_subscription_id);
                    
                    \Log::info('Stripe subscription cancelled by admin', [
                        'user_program_id' => $userProgram->id,
                        'stripe_subscription_id' => $userProgram->stripe_subscription_id,
                        'admin_id' => auth()->id(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to cancel Stripe subscription: ' . $e->getMessage(), [
                        'user_program_id' => $userProgram->id,
                        'stripe_subscription_id' => $userProgram->stripe_subscription_id,
                    ]);
                    // Continue with cancellation even if Stripe cancellation fails
                }
            }

            // Update program status to cancelled
            $adminNotes = $userProgram->admin_notes ?? '';
            $adminNotes .= "\n\n[CANCELLED BY ADMIN]\n";
            if ($request->cancellation_reason) {
                $adminNotes .= "Reason: " . $request->cancellation_reason . "\n";
            }
            $adminNotes .= "Cancelled by: " . auth()->user()->name . " (ID: " . auth()->id() . ")\n";
            $adminNotes .= "Cancelled at: " . now()->format('Y-m-d H:i:s') . "\n";
            if ($userProgram->stripe_subscription_id) {
                $adminNotes .= "Stripe Subscription ID: " . $userProgram->stripe_subscription_id;
            }

            $userProgram->update([
                'status' => UserProgram::STATUS_CANCELLED,
                'admin_notes' => $adminNotes,
            ]);

            return redirect()->back()->with('success', 'Subscription cancelled successfully.');
        } catch (\Exception $e) {
            \Log::error('Admin subscription cancellation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel subscription. Please try again.');
        }
    }
}