<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions
     */
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'program'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new subscription
     */
    public function create()
    {
        $programs = Program::where('is_subscription_based', true)->get();
        $users = User::where('is_admin', false)->get();

        return view('admin.subscriptions.create', compact('programs', 'users'));
    }

    /**
     * Store a newly created subscription
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:programs,id',
            'subscription_type' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'monthly_sessions' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'subscription_features' => 'nullable|array',
            'subscription_features.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if user already has an active subscription for this program
        $existingSubscription = Subscription::where('user_id', $request->user_id)
            ->where('program_id', $request->program_id)
            ->where('is_active', true)
            ->first();

        if ($existingSubscription) {
            return redirect()->back()
                ->withErrors(['user_id' => 'User already has an active subscription for this program.'])
                ->withInput();
        }

        $subscription = Subscription::create([
            ...$request->all(),
            'next_billing_date' => Carbon::parse($request->starts_at)->addMonth(),
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription created successfully!');
    }

    /**
     * Display the specified subscription
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'program', 'bookings']);
        
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the subscription
     */
    public function edit(Subscription $subscription)
    {
        $programs = Program::where('is_subscription_based', true)->get();
        $users = User::where('is_admin', false)->get();

        return view('admin.subscriptions.edit', compact('subscription', 'programs', 'users'));
    }

    /**
     * Update the specified subscription
     */
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'subscription_type' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'monthly_sessions' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'subscription_features' => 'nullable|array',
            'subscription_features.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $subscription->update($request->all());

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription updated successfully!');
    }

    /**
     * Remove the specified subscription
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted successfully!');
    }

    /**
     * Toggle subscription active status
     */
    public function toggleStatus(Subscription $subscription)
    {
        $subscription->update(['is_active' => !$subscription->is_active]);

        $status = $subscription->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Subscription {$status} successfully!");
    }

    /**
     * Reset monthly booking count
     */
    public function resetMonthlyCount(Subscription $subscription)
    {
        $subscription->update([
            'total_bookings_this_month' => 0,
            'last_billing_date' => now(),
            'next_billing_date' => now()->addMonth(),
        ]);

        return redirect()->back()
            ->with('success', 'Monthly booking count reset successfully!');
    }

    /**
     * Extend subscription
     */
    public function extend(Request $request, Subscription $subscription)
    {
        $request->validate([
            'extension_months' => 'required|integer|min:1|max:12',
        ]);

        $currentEndDate = $subscription->ends_at ?: now();
        $newEndDate = Carbon::parse($currentEndDate)->addMonths($request->extension_months);

        $subscription->update([
            'ends_at' => $newEndDate,
        ]);

        return redirect()->back()
            ->with('success', "Subscription extended by {$request->extension_months} month(s)!");
    }
}
