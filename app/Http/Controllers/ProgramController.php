<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Program;
use App\Models\UserProgram;
use Illuminate\Support\Facades\Storage;
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
            
            return redirect()->back()->with('success', 'Program re-selected successfully! Your application will be reviewed by our team.');
        } else {
            // Create new user program selection
            UserProgram::create([
                'user_id' => $user->id,
                'program_id' => $program->id,
                'status' => UserProgram::STATUS_PENDING,
            ]);

            return redirect()->back()->with('success', 'Program selected successfully! Your application will be reviewed by our team.');
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

        // Use the static PDF template instead of generating dynamic content
        $templatePath = 'agreement-templates/life-coaching-contract.pdf';
        $templateFullPath = storage_path('app/public/' . $templatePath);
        
        // Check if template exists
        if (!file_exists($templateFullPath)) {
            abort(404, 'Agreement template not found.');
        }

        // Return the static PDF for download
        return response()->download($templateFullPath, 'life_coaching_contract.pdf');
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
}