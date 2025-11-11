<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Program;
use App\Models\UserProgram;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProgramAgreementSent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    /**
     * Display available programs for client selection
     */
    public function index()
    {
        $programs = Program::active()->get();
        $userPrograms = Auth::user()->userPrograms()->with('program')->get();
        
        return view('client.programs', compact('programs', 'userPrograms'));
    }

    /**
     * Client selects a program
     */
    public function selectProgram(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $user = Auth::user();
        $program = Program::findOrFail($request->program_id);

        // Check if user already has this program (excluding cancelled programs)
        $existingProgram = $user->userPrograms()
            ->where('program_id', $program->id)
            ->where('status', '!=', UserProgram::STATUS_CANCELLED)
            ->first();
        
        if ($existingProgram) {
            return redirect()->back()->with('error', 'You have already selected this program.');
        }

        // Check if user previously cancelled this program
        $cancelledProgram = $user->userPrograms()
            ->where('program_id', $program->id)
            ->where('status', UserProgram::STATUS_CANCELLED)
            ->first();

        if ($cancelledProgram) {
            // Reactivate the previously cancelled program
            $cancelledProgram->update([
                'status' => UserProgram::STATUS_PENDING,
                'admin_notes' => $cancelledProgram->admin_notes . "\n\n[RE-SELECTED BY CLIENT] Program re-selected after cancellation - " . now()->format('Y-m-d H:i:s'),
            ]);
            
            // Auto-send agreement if not already sent
            if (!$cancelledProgram->agreement_path) {
                $this->autoSendAgreement($cancelledProgram);
            }
            
            return redirect()->back()->with('success', 'Program re-selected successfully! Please download and sign the agreement to proceed.');
        } else {
            // Create new user program selection
            $userProgram = UserProgram::create([
                'user_id' => $user->id,
                'program_id' => $program->id,
                'status' => UserProgram::STATUS_PENDING,
            ]);

            // Auto-send agreement immediately
            $this->autoSendAgreement($userProgram);

            return redirect()->back()->with('success', 'Program selected successfully! Please download and sign the agreement to proceed.');
        }
    }

    /**
     * Download program agreement
     */
    public function downloadAgreement(UserProgram $userProgram)
    {
        // Verify the user owns this program selection
        if ($userProgram->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        // Get program-specific agreement template or fallback to default
        $templatePath = $userProgram->program->agreement_template_path ?? 'agreement-templates/life-coaching-contract.pdf';
        $templateFullPath = storage_path('app/public/' . $templatePath);
        
        // Check if template exists
        if (!file_exists($templateFullPath)) {
            abort(404, 'Agreement template not found for this program.');
        }

        // Generate download filename
        $programName = Str::slug($userProgram->program->name);
        $fileName = $programName . '_agreement.pdf';

        // Return the static PDF for download
        return response()->download($templateFullPath, $fileName);
    }

    /**
     * Upload signed agreement
     */
    public function uploadSignedAgreement(Request $request, UserProgram $userProgram)
    {
        // Verify the user owns this program selection
        if ($userProgram->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'signed_agreement' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        // Store the signed agreement
        $file = $request->file('signed_agreement');
        $fileName = 'signed_agreement_' . $userProgram->id . '_' . time() . '.pdf';
        $filePath = $file->storeAs('signed-agreements', $fileName, 'public');

        // Update user program
        $userProgram->update([
            'signed_agreement_path' => $filePath,
            'signed_agreement_name' => $file->getClientOriginalName(),
            'agreement_uploaded_at' => now(),
            'status' => UserProgram::STATUS_AGREEMENT_UPLOADED,
        ]);

        return redirect()->back()->with('success', 'Signed agreement uploaded successfully! We will review it and get back to you.');
    }

    /**
     * Client cancels a selected program
     */
    public function cancelProgram(Request $request, UserProgram $userProgram)
    {
        // Verify the user owns this program selection
        if ($userProgram->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // Check if program can be cancelled
        if (!$userProgram->canBeCancelled()) {
            return redirect()->back()->with('error', 'This program cannot be cancelled.');
        }

        // Update program status to cancelled
        $userProgram->update([
            'status' => UserProgram::STATUS_CANCELLED,
            'admin_notes' => $userProgram->admin_notes . "\n\n[CANCELLED BY CLIENT] Reason: " . $request->cancellation_reason . " - " . now()->format('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Program cancelled successfully. We\'re sorry to see you go!');
    }

    /**
     * View program details
     */
    public function show(Program $program)
    {
        return view('client.program-details', compact('program'));
    }

    /**
     * Show payment selection page
     */
    public function paymentSelection(UserProgram $userProgram)
    {
        // Verify the user owns this program selection
        if ($userProgram->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        // Check if program is approved
        if ($userProgram->status !== UserProgram::STATUS_APPROVED) {
            return redirect()->route('client.programs')
                ->with('error', 'This program is not yet approved for payment.');
        }

        // Calculate one-time payment amount
        $oneTimeAmount = $userProgram->program->one_time_payment_amount ?? 
                        (($userProgram->program->monthly_price ?? 0) * 3);

        return view('client.payment-selection', compact('userProgram', 'oneTimeAmount'));
    }

    /**
     * Show checkout page
     */
    public function checkout(Request $request, UserProgram $userProgram)
    {
        // Verify the user owns this program selection
        if ($userProgram->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        // Check if program is approved
        if ($userProgram->status !== UserProgram::STATUS_APPROVED) {
            return redirect()->route('client.programs')
                ->with('error', 'This program is not yet approved for payment.');
        }

        $request->validate([
            'payment_type' => 'required|in:monthly,one_time',
        ]);

        $paymentType = $request->payment_type;
        
        // Calculate one-time payment amount
        $oneTimeAmount = $userProgram->program->one_time_payment_amount ?? 
                        (($userProgram->program->monthly_price ?? 0) * 3);

        return view('client.checkout', compact('userProgram', 'paymentType', 'oneTimeAmount'));
    }

    /**
     * Process checkout submission
     */
    public function checkoutSubmit(Request $request, UserProgram $userProgram)
    {
        // Verify the user owns this program selection
        if ($userProgram->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        // Check if program is approved
        if ($userProgram->status !== UserProgram::STATUS_APPROVED) {
            return redirect()->route('client.programs')
                ->with('error', 'This program is not yet approved for payment.');
        }

        $request->validate([
            'payment_type' => 'required|in:monthly,one_time',
            'payment_method' => 'required|in:credit_card',
            'terms_accepted' => 'required|accepted',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_state' => 'required|string|max:255',
            'billing_postal_code' => 'required|string|max:20',
            'billing_country' => 'required|string|max:255',
            // Credit card fields (required)
            'card_holder_name' => 'required|string|max:255',
            'card_number' => 'required|string|max:19',
            'expiry_month' => 'required|string|max:2',
            'expiry_year' => 'required|string|max:4',
            'cvv' => 'required|string|max:4',
        ]);

        // Determine payment type
        $paymentType = $request->payment_type === 'monthly' 
            ? UserProgram::PAYMENT_TYPE_MONTHLY 
            : UserProgram::PAYMENT_TYPE_ONE_TIME;

        // Initialize contract
        if (!$userProgram->contract_duration_months) {
            $userProgram->update(['contract_duration_months' => 3]);
        }
        $userProgram->initializeContract($paymentType);

        // Calculate payment amount
        $paymentAmount = $paymentType === UserProgram::PAYMENT_TYPE_ONE_TIME
            ? ($userProgram->program->one_time_payment_amount ?? (($userProgram->program->monthly_price ?? 0) * 3))
            : ($userProgram->program->monthly_price ?? 0);

        // Generate payment reference
        $paymentReference = 'PAY-' . strtoupper(Str::random(8)) . '-' . time();

        // Create payment record
        $payment = Payment::create([
            'user_program_id' => $userProgram->id,
            'payment_type' => $paymentType === UserProgram::PAYMENT_TYPE_ONE_TIME 
                ? Payment::TYPE_CONTRACT_ONE_TIME 
                : Payment::TYPE_CONTRACT_MONTHLY,
            'status' => Payment::STATUS_COMPLETED,
            'amount' => $paymentAmount,
            'payment_reference' => $paymentReference,
            'month_number' => $paymentType === UserProgram::PAYMENT_TYPE_MONTHLY ? 1 : null,
            'paid_at' => now(),
            'notes' => 'Payment completed via checkout. Card: **** **** **** ' . substr(str_replace(' ', '', $request->card_number), -4),
        ]);

        // Update user program - activate immediately
        $updateData = [
            'payment_type' => $paymentType,
            'status' => UserProgram::STATUS_ACTIVE,
            'payment_completed_at' => now(),
            'amount_paid' => $paymentAmount,
            'payment_reference' => $paymentReference,
            'payments_completed' => 1,
        ];

        // If monthly payment, set next payment date
        if ($paymentType === UserProgram::PAYMENT_TYPE_MONTHLY) {
            $updateData['next_payment_date'] = now()->addMonth();
        }

        $userProgram->update($updateData);

        // Update admin notes with payment information
        $adminNotes = $userProgram->admin_notes ?? '';
        $adminNotes .= "\n\n[PAYMENT COMPLETED & PROGRAM ACTIVATED]\n";
        $adminNotes .= "Payment Type: " . ($request->payment_type === 'monthly' ? 'Monthly' : 'One-Time') . "\n";
        $adminNotes .= "Payment Method: Credit Card\n";
        $adminNotes .= "Amount: $" . number_format($paymentAmount, 2) . "\n";
        $adminNotes .= "Payment Reference: " . $paymentReference . "\n";
        $adminNotes .= "Card: **** **** **** " . substr(str_replace(' ', '', $request->card_number), -4) . "\n";
        $adminNotes .= "Card Holder: " . $request->card_holder_name . "\n";
        $adminNotes .= "Expiry: " . $request->expiry_month . '/' . $request->expiry_year . "\n";
        $adminNotes .= "Billing Address: " . $request->billing_address . ", " . $request->billing_city . ", " . $request->billing_state . " " . $request->billing_postal_code . ", " . $request->billing_country . "\n";
        $adminNotes .= "Completed: " . now()->format('Y-m-d H:i:s');

        $userProgram->update(['admin_notes' => $adminNotes]);

        return redirect()->route('client.programs')
            ->with('success', 'Payment completed successfully! Your program has been activated. You can now book sessions!');
    }

    /**
     * Auto-send agreement when client selects program
     */
    private function autoSendAgreement(UserProgram $userProgram)
    {
        // Get program-specific agreement template or fallback to default
        $templatePath = $userProgram->program->agreement_template_path ?? 'agreement-templates/life-coaching-contract.pdf';
        $templateFullPath = storage_path('app/public/' . $templatePath);
        
        // Check if template exists
        if (!file_exists($templateFullPath)) {
            \Log::warning('Agreement template not found for program: ' . $userProgram->program->name);
            return;
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
    }
}