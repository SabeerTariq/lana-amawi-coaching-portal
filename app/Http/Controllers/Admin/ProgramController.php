<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\UserProgram;
use App\Models\User;
use App\Models\Subscription;
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
        $allApplications = UserProgram::with(['user', 'program'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $applications = $allApplications->groupBy('status');

        // Debug: Log the grouped applications
        \Log::info('All applications count:', ['count' => $allApplications->count()]);
        \Log::info('Grouped applications keys:', $applications->keys()->toArray());
        \Log::info('Grouped applications:', $applications->toArray());

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
        // Use the static PDF template instead of generating dynamic content
        $templatePath = 'agreement-templates/life-coaching-contract.pdf';
        $templateFullPath = storage_path('app/public/' . $templatePath);
        
        // Check if template exists
        if (!file_exists($templateFullPath)) {
            return redirect()->back()->with('error', 'Agreement template not found.');
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
    public function requestPayment(UserProgram $userProgram)
    {
        $userProgram->update([
            'status' => UserProgram::STATUS_PAYMENT_REQUESTED,
            'payment_requested_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Payment requested for ' . $userProgram->user->name . '!');
    }

    /**
     * Mark payment as completed
     */
    public function markPaymentCompleted(Request $request, UserProgram $userProgram)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_reference' => 'required|string|max:255',
        ]);

        $userProgram->update([
            'status' => UserProgram::STATUS_PAYMENT_COMPLETED,
            'payment_completed_at' => now(),
            'amount_paid' => $request->amount_paid,
            'payment_reference' => $request->payment_reference,
        ]);

        return redirect()->back()->with('success', 'Payment marked as completed for ' . $userProgram->user->name . '!');
    }

    /**
     * Activate program
     */
    public function activateProgram(UserProgram $userProgram)
    {
        $userProgram->update([
            'status' => UserProgram::STATUS_ACTIVE,
        ]);

        return redirect()->back()->with('success', 'Program activated for ' . $userProgram->user->name . '!');
    }

    /**
     * Reject application
     */
    public function rejectApplication(Request $request, UserProgram $userProgram)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $userProgram->update([
            'status' => UserProgram::STATUS_REJECTED,
            'admin_notes' => $request->admin_notes,
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
        $programs = Program::withCount(['userPrograms', 'subscriptions'])
            ->orderBy('created_at', 'desc')
            ->get();

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
            'price' => 'required|numeric|min:0',
            'duration_months' => 'nullable|integer|min:1',
            'sessions_included' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'subscription_type' => 'nullable|string|max:255',
            'monthly_price' => 'nullable|numeric|min:0',
            'monthly_sessions' => 'nullable|integer|min:1',
            'booking_limit_per_month' => 'nullable|integer|min:0',
            'is_subscription_based' => 'boolean',
            'subscription_features' => 'nullable|array',
            'subscription_features.*' => 'string|max:255',
        ]);

        $program = Program::create($request->all());

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
            'price' => 'required|numeric|min:0',
            'duration_months' => 'nullable|integer|min:1',
            'sessions_included' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'subscription_type' => 'nullable|string|max:255',
            'monthly_price' => 'nullable|numeric|min:0',
            'monthly_sessions' => 'nullable|integer|min:1',
            'booking_limit_per_month' => 'nullable|integer|min:0',
            'is_subscription_based' => 'boolean',
            'subscription_features' => 'nullable|array',
            'subscription_features.*' => 'string|max:255',
        ]);

        $program->update($request->all());

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
}