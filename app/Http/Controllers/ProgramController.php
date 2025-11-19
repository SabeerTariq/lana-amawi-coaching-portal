<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Program;
use App\Models\UserProgram;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProgramAgreementSent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentIntent;

class ProgramController extends Controller
{
    /**
     * Display available programs for client selection
     */
    public function index()
    {
        $programs = Program::active()->get();
        // Exclude cancelled programs from "My Program Applications" section
        $userPrograms = Auth::user()->userPrograms()
            ->where('status', '!=', UserProgram::STATUS_CANCELLED)
            ->with('program')
            ->get();
        
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
            $updateData = [
                'admin_notes' => $cancelledProgram->admin_notes . "\n\n[RE-SELECTED BY CLIENT] Program re-selected after cancellation - " . now()->format('Y-m-d H:i:s'),
            ];
            
            // Auto-send agreement if not already sent
            if (!$cancelledProgram->agreement_path) {
                // No agreement exists, send it (autoSendAgreement will set status to AGREEMENT_SENT)
                $this->autoSendAgreement($cancelledProgram);
                // Update admin notes separately since autoSendAgreement doesn't update them
                $cancelledProgram->update(['admin_notes' => $updateData['admin_notes']]);
            } else {
                // Agreement already exists from before, set status to AGREEMENT_SENT so client can see it
                $updateData['status'] = UserProgram::STATUS_AGREEMENT_SENT;
                // Update agreement_sent_at if it's null
                if (!$cancelledProgram->agreement_sent_at) {
                    $updateData['agreement_sent_at'] = now();
                }
                $cancelledProgram->update($updateData);
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
     * Client cancels a selected program/subscription
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'This program cannot be cancelled.',
                ], 400);
            }
            return redirect()->back()->with('error', 'This program cannot be cancelled.');
        }

        try {
            // Cancel Stripe subscription if it's a monthly subscription
            if ($userProgram->payment_type === UserProgram::PAYMENT_TYPE_MONTHLY && $userProgram->stripe_subscription_id) {
                try {
                    $stripeService = new StripeService();
                    $stripeService->cancelSubscription($userProgram->stripe_subscription_id);
                    
                    \Log::info('Stripe subscription cancelled', [
                        'user_program_id' => $userProgram->id,
                        'stripe_subscription_id' => $userProgram->stripe_subscription_id,
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
            $adminNotes .= "\n\n[CANCELLED BY CLIENT]\n";
            $adminNotes .= "Reason: " . $request->cancellation_reason . "\n";
            $adminNotes .= "Cancelled at: " . now()->format('Y-m-d H:i:s') . "\n";
            if ($userProgram->stripe_subscription_id) {
                $adminNotes .= "Stripe Subscription ID: " . $userProgram->stripe_subscription_id;
            }

            $userProgram->update([
                'status' => UserProgram::STATUS_CANCELLED,
                'admin_notes' => $adminNotes,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Program cancelled successfully. We\'re sorry to see you go!',
                ]);
            }

            return redirect()->back()->with('success', 'Program cancelled successfully. We\'re sorry to see you go!');
        } catch (\Exception $e) {
            \Log::error('Program cancellation failed: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to cancel program. Please try again or contact support.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to cancel program. Please try again or contact support.');
        }
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

        // Get ISO 3166-1 alpha-2 country codes for Stripe
        $countries = $this->getIsoCountries();
        
        // Get Stripe key from database settings, fallback to config
        $stripeKey = \App\Models\Setting::get('stripe_key', config('services.stripe.key'));

        return view('client.checkout', compact('userProgram', 'paymentType', 'oneTimeAmount', 'countries', 'stripeKey'));
    }

    /**
     * Create payment intent for Stripe
     */
    public function createPaymentIntent(Request $request, UserProgram $userProgram)
    {
        // Verify the user owns this program selection
        if ($userProgram->user_id !== Auth::id()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'payment_type' => 'required|in:monthly,one_time',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Log the incoming payload for debugging
        \Log::info('Payment Intent Request Payload:', [
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
            'user_program_id' => $userProgram->id,
            'program_id' => $userProgram->program->id,
            'monthly_price' => $userProgram->program->monthly_price,
            'one_time_amount' => $userProgram->program->one_time_payment_amount,
        ]);

        try {
            $stripeService = new StripeService();
            $user = Auth::user();
            
            // Create or retrieve Stripe customer
            if (!$userProgram->stripe_customer_id) {
                $customer = $stripeService->createCustomer(
                    $user->email,
                    $user->name,
                    [
                        'user_id' => $user->id,
                        'user_program_id' => $userProgram->id,
                    ]
                );
                $userProgram->update(['stripe_customer_id' => $customer->id]);
            } else {
                $customer = Customer::retrieve($userProgram->stripe_customer_id);
            }

            if ($request->payment_type === 'one_time') {
                // One-time payment
                $packageName = $userProgram->program->name;
                $packageType = $userProgram->program->formatted_subscription_type;
                $packageDescription = $userProgram->program->description ? 
                    Str::limit($userProgram->program->description, 200) : 
                    $packageType . ' Program';
                
                $description = $packageName . ' (' . $packageType . ') - ' . $packageDescription;
                
                $paymentIntent = $stripeService->createPaymentIntent(
                    $request->amount,
                    'usd',
                    $customer->id,
                    [
                        'user_program_id' => $userProgram->id,
                        'program_id' => $userProgram->program->id,
                        'payment_type' => 'one_time',
                        'package_name' => $packageName,
                        'package_type' => $packageType,
                    ],
                    $description
                );

                return response()->json([
                    'payment_intent' => true,
                    'client_secret' => $paymentIntent->client_secret,
                ]);
            } else {
                // Monthly subscription
                \Log::info('Creating monthly subscription', [
                    'amount' => $request->amount,
                    'customer_id' => $customer->id ?? 'not set',
                ]);
                
                // Create product if not exists
                $packageName = $userProgram->program->name;
                $packageType = $userProgram->program->formatted_subscription_type;
                $productName = $packageName . ' (' . $packageType . ') - Monthly Subscription';
                $productDescription = $userProgram->program->description ? 
                    $userProgram->program->description . ' - Monthly subscription for 3 months' : 
                    $packageType . ' Program - Monthly subscription for 3 months';
                
                \Log::info('Creating Stripe product', [
                    'name' => $productName,
                    'description' => $productDescription,
                ]);
                
                $product = $stripeService->createProduct(
                    $productName,
                    $productDescription,
                    [
                        'user_program_id' => $userProgram->id,
                        'program_id' => $userProgram->program->id,
                        'package_name' => $packageName,
                        'package_type' => $packageType,
                    ]
                );

                \Log::info('Stripe product created', ['product_id' => $product->id]);

                // Create price
                \Log::info('Creating Stripe price', [
                    'product_id' => $product->id,
                    'amount' => $request->amount,
                    'currency' => 'usd',
                    'interval' => 'month',
                ]);
                
                $price = $stripeService->createPrice(
                    $product->id,
                    $request->amount,
                    'usd',
                    'month'
                );
                
                \Log::info('Stripe price created', ['price_id' => $price->id]);

                // Create subscription
                \Log::info('Creating Stripe subscription', [
                    'customer_id' => $customer->id,
                    'price_id' => $price->id,
                ]);
                
                $subscription = $stripeService->createSubscription(
                    $customer->id,
                    $price->id,
                    [
                        'user_program_id' => $userProgram->id,
                        'program_id' => $userProgram->program->id,
                        'package_name' => $packageName,
                        'package_type' => $packageType,
                    ]
                );
                
                \Log::info('Stripe subscription created', [
                    'subscription_id' => $subscription->id,
                    'latest_invoice' => is_string($subscription->latest_invoice) 
                        ? $subscription->latest_invoice 
                        : ($subscription->latest_invoice->id ?? 'unknown'),
                ]);

                // Update user program with subscription info
                $userProgram->update([
                    'stripe_subscription_id' => $subscription->id,
                    'stripe_price_id' => $price->id,
                ]);

                // Get client secret from latest invoice payment intent
                $clientSecret = null;
                
                // Get invoice ID (handle both string and object)
                $invoiceId = is_string($subscription->latest_invoice) 
                    ? $subscription->latest_invoice 
                    : (isset($subscription->latest_invoice->id) ? $subscription->latest_invoice->id : null);
                
                if (!$invoiceId) {
                    \Log::error('No invoice ID found in subscription: ' . $subscription->id);
                    throw new \Exception('Failed to retrieve invoice from subscription');
                }
                
                // Retrieve invoice with payment intent expanded using proper Stripe SDK method
                try {
                    $invoice = Invoice::retrieve($invoiceId, [
                        'expand' => ['payment_intent'],
                    ]);
                    
                    \Log::info('Invoice retrieved', [
                        'invoice_id' => $invoice->id,
                        'invoice_status' => $invoice->status,
                        'payment_intent' => $invoice->payment_intent ? (is_object($invoice->payment_intent) ? $invoice->payment_intent->id : $invoice->payment_intent) : 'null',
                    ]);
                } catch (\Exception $e) {
                    // Fallback: retrieve without expand and then get payment intent separately
                    \Log::warning('Failed to retrieve invoice with expand, trying without: ' . $e->getMessage());
                    $invoice = Invoice::retrieve($invoiceId);
                }
                
                // Get payment intent ID
                $paymentIntentId = null;
                if ($invoice->payment_intent) {
                    if (is_object($invoice->payment_intent)) {
                        $paymentIntentId = $invoice->payment_intent->id;
                    } else if (is_string($invoice->payment_intent)) {
                        $paymentIntentId = $invoice->payment_intent;
                    }
                }
                
                // If invoice doesn't have payment_intent, it might be in draft status
                // We need to finalize it or wait for it to be created
                if (!$paymentIntentId) {
                    \Log::warning('Invoice does not have payment_intent yet', [
                        'invoice_id' => $invoice->id,
                        'invoice_status' => $invoice->status,
                        'invoice_subscription' => $invoice->subscription,
                        'invoice_amount_due' => $invoice->amount_due ?? 'unknown',
                    ]);
                    
                    // If invoice is draft, finalize it to create payment intent
                    if ($invoice->status === 'draft') {
                        \Log::info('Finalizing draft invoice in controller');
                        try {
                            $invoice = $invoice->finalizeInvoice(['expand' => ['payment_intent']]);
                            
                            // Re-retrieve with payment intent
                            $invoice = Invoice::retrieve($invoice->id, [
                                'expand' => ['payment_intent'],
                            ]);
                            
                            \Log::info('Invoice finalized in controller', [
                                'invoice_status' => $invoice->status,
                                'has_payment_intent' => $invoice->payment_intent ? 'yes' : 'no',
                            ]);
                            
                            if ($invoice->payment_intent) {
                                $paymentIntentId = is_object($invoice->payment_intent) 
                                    ? $invoice->payment_intent->id 
                                    : $invoice->payment_intent;
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to finalize invoice: ' . $e->getMessage());
                            throw $e;
                        }
                    } else if ($invoice->status === 'open') {
                        // Invoice is open but has no payment_intent
                        // This happens when subscription is created with default_incomplete
                        // We need to pay the invoice which will create a payment_intent
                        // But first, we need to create a payment intent manually and link it
                        \Log::warning('Invoice is open but has no payment_intent');
                        
                        try {
                            // For open invoices without payment_intent, we need to create one
                            // and then pay the invoice with it
                            // However, Stripe's recommended approach is to use the subscription's
                            // pending_setup_intent or create a payment_intent for the invoice amount
                            
                            // Try to pay the invoice which should create a payment_intent
                            // But we can't pay without a payment method, so we create a payment_intent
                            // that the client will confirm with their payment method
                            $paymentIntent = PaymentIntent::create([
                                'amount' => $invoice->amount_due,
                                'currency' => $invoice->currency ?? 'usd',
                                'customer' => $customer->id,
                                'metadata' => [
                                    'invoice_id' => $invoice->id,
                                    'subscription_id' => $subscription->id,
                                    'user_program_id' => $userProgram->id,
                                    'type' => 'subscription_payment',
                                ],
                                'description' => 'Subscription payment for ' . $packageName,
                                'automatic_payment_methods' => [
                                    'enabled' => true,
                                ],
                            ]);
                            
                            $paymentIntentId = $paymentIntent->id;
                            $clientSecret = $paymentIntent->client_secret;
                            
                            \Log::info('Payment intent created manually for open invoice', [
                                'payment_intent_id' => $paymentIntentId,
                                'invoice_id' => $invoice->id,
                                'amount' => $invoice->amount_due,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to create payment intent for open invoice: ' . $e->getMessage());
                            \Log::error('Exception details: ' . $e->getTraceAsString());
                            // Fall through to error handling
                        }
                    }
                }
                
                if ($paymentIntentId && !isset($clientSecret)) {
                    // Retrieve payment intent to get client secret
                    $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
                    $clientSecret = $paymentIntent->client_secret;
                    \Log::info('Payment intent retrieved', [
                        'payment_intent_id' => $paymentIntentId,
                        'client_secret' => substr($clientSecret, 0, 20) . '...',
                    ]);
                }

                if (!isset($clientSecret) || !$clientSecret) {
                    \Log::error('Failed to get client secret from subscription: ' . $subscription->id);
                    \Log::error('Invoice ID: ' . $invoiceId);
                    \Log::error('Invoice Status: ' . ($invoice->status ?? 'unknown'));
                    \Log::error('Payment Intent ID: ' . ($paymentIntentId ?? 'null'));
                    throw new \Exception('Failed to retrieve payment intent from subscription. Invoice may need to be finalized.');
                }

                return response()->json([
                    'subscription' => true,
                    'subscription_id' => $subscription->id,
                    'client_secret' => $clientSecret,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Stripe payment intent creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to create payment intent. Please try again.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'This program is not yet approved for payment.',
                ], 403);
            }
            return redirect()->route('client.programs')
                ->with('error', 'This program is not yet approved for payment.');
        }

        try {
            $request->validate([
                'payment_type' => 'required|in:monthly,one_time',
                'payment_intent_id' => 'required|string',
                'terms_accepted' => 'required|accepted',
                'billing_address' => 'required|string|max:255',
                'billing_city' => 'required|string|max:255',
                'billing_state' => 'required|string|max:255',
                'billing_postal_code' => 'required|string|max:20',
                'billing_country' => 'required|string|size:2|regex:/^[A-Z]{2}$/',
            ], [
                'billing_country.size' => 'Country code must be exactly 2 letters.',
                'billing_country.regex' => 'Country code must be a valid 2-letter ISO code (e.g., US, GB, CA).',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed.',
                    'errors' => $e->errors(),
                    'message' => 'Please check your input and try again.',
                ], 422);
            }
            throw $e;
        }

        try {
            // Verify payment intent with Stripe (expand charges to get charge ID)
            $stripeService = new StripeService();
            $paymentIntent = $stripeService->retrievePaymentIntent($request->payment_intent_id, ['expand' => ['charges']]);

            // Check if payment was successful
            if ($paymentIntent->status !== 'succeeded') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Payment was not successful. Please try again.',
                        'message' => 'Payment was not successful. Please try again.',
                    ], 400);
                }
                return redirect()->back()
                    ->with('error', 'Payment was not successful. Please try again.')
                    ->withInput();
            }

            // Determine payment type
            $paymentType = $request->payment_type === 'monthly' 
                ? UserProgram::PAYMENT_TYPE_MONTHLY 
                : UserProgram::PAYMENT_TYPE_ONE_TIME;

            // Initialize contract
            if (!$userProgram->contract_duration_months) {
                $userProgram->update(['contract_duration_months' => 3]);
            }
            $userProgram->initializeContract($paymentType);

            // Calculate payment amount from Stripe (convert from cents)
            $paymentAmount = $paymentIntent->amount / 100;

            // Generate payment reference
            $paymentReference = 'PAY-' . strtoupper(Str::random(8)) . '-' . time();

            // Get charge ID if available
            $chargeId = null;
            if (isset($paymentIntent->charges) && 
                is_object($paymentIntent->charges) && 
                isset($paymentIntent->charges->data) && 
                is_array($paymentIntent->charges->data) && 
                count($paymentIntent->charges->data) > 0) {
                $chargeId = $paymentIntent->charges->data[0]->id;
            } elseif (isset($paymentIntent->latest_charge) && is_string($paymentIntent->latest_charge)) {
                // Fallback: use latest_charge if available
                $chargeId = $paymentIntent->latest_charge;
            }

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
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_charge_id' => $chargeId,
                'stripe_customer_id' => $userProgram->stripe_customer_id,
                'notes' => 'Payment completed via Stripe. Payment Intent: ' . $paymentIntent->id,
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
            $adminNotes .= "Payment Method: Stripe (Credit Card)\n";
            $adminNotes .= "Amount: $" . number_format($paymentAmount, 2) . "\n";
            $adminNotes .= "Payment Reference: " . $paymentReference . "\n";
            $adminNotes .= "Stripe Payment Intent: " . $paymentIntent->id . "\n";
            if ($chargeId) {
                $adminNotes .= "Stripe Charge: " . $chargeId . "\n";
            }
            if ($request->subscription_id) {
                $adminNotes .= "Stripe Subscription: " . $request->subscription_id . "\n";
            }
            $adminNotes .= "Billing Address: " . $request->billing_address . ", " . $request->billing_city . ", " . $request->billing_state . " " . $request->billing_postal_code . ", " . $request->billing_country . "\n";
            $adminNotes .= "Completed: " . now()->format('Y-m-d H:i:s');

            $userProgram->update(['admin_notes' => $adminNotes]);

            // If AJAX request, return JSON with redirect URL
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment completed successfully! Your program has been activated.',
                    'redirect' => route('client.programs.checkout.success', $userProgram),
                ]);
            }

            return redirect()->route('client.programs.checkout.success', $userProgram)
                ->with('success', 'Payment completed successfully! Your program has been activated.')
                ->with('payment_reference', $paymentReference);
        } catch (\Exception $e) {
            \Log::error('Checkout submission failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If AJAX request, return JSON error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment processing failed. Please try again or contact support.',
                    'message' => config('app.debug') ? $e->getMessage() : 'Payment processing failed. Please try again or contact support.',
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Payment processing failed. Please try again or contact support.')
                ->withInput();
        }
    }

    /**
     * Show payment success page
     */
    public function checkoutSuccess(UserProgram $userProgram)
    {
        // Verify the user owns this program selection
        if ($userProgram->user_id !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        // Check if program is active (payment was successful)
        if ($userProgram->status !== UserProgram::STATUS_ACTIVE) {
            return redirect()->route('client.programs')
                ->with('error', 'Payment not found or program not activated.');
        }

        return view('client.checkout-success', compact('userProgram'));
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

    /**
     * Get ISO 3166-1 alpha-2 country codes for Stripe
     * Returns array of country codes => country names (sorted alphabetically by name)
     */
    private function getIsoCountries()
    {
        $countries = [
            'AD' => 'Andorra',
            'AE' => 'United Arab Emirates',
            'AG' => 'Antigua and Barbuda',
            'AI' => 'Anguilla',
            'AL' => 'Albania',
            'AM' => 'Armenia',
            'AO' => 'Angola',
            'AR' => 'Argentina',
            'AT' => 'Austria',
            'AU' => 'Australia',
            'AW' => 'Aruba',
            'AZ' => 'Azerbaijan',
            'BA' => 'Bosnia and Herzegovina',
            'BB' => 'Barbados',
            'BD' => 'Bangladesh',
            'BE' => 'Belgium',
            'BF' => 'Burkina Faso',
            'BG' => 'Bulgaria',
            'BH' => 'Bahrain',
            'BI' => 'Burundi',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BN' => 'Brunei',
            'BO' => 'Bolivia',
            'BR' => 'Brazil',
            'BS' => 'Bahamas',
            'BT' => 'Bhutan',
            'BW' => 'Botswana',
            'BY' => 'Belarus',
            'BZ' => 'Belize',
            'CA' => 'Canada',
            'CD' => 'Congo (DRC)',
            'CF' => 'Central African Republic',
            'CG' => 'Congo',
            'CH' => 'Switzerland',
            'CI' => 'Côte d\'Ivoire',
            'CL' => 'Chile',
            'CM' => 'Cameroon',
            'CN' => 'China',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'CV' => 'Cape Verde',
            'CW' => 'Curaçao',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DE' => 'Germany',
            'DJ' => 'Djibouti',
            'DK' => 'Denmark',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'DZ' => 'Algeria',
            'EC' => 'Ecuador',
            'EE' => 'Estonia',
            'EG' => 'Egypt',
            'ER' => 'Eritrea',
            'ES' => 'Spain',
            'ET' => 'Ethiopia',
            'FI' => 'Finland',
            'FJ' => 'Fiji',
            'FM' => 'Micronesia',
            'FR' => 'France',
            'GA' => 'Gabon',
            'GB' => 'United Kingdom',
            'GD' => 'Grenada',
            'GE' => 'Georgia',
            'GH' => 'Ghana',
            'GM' => 'Gambia',
            'GN' => 'Guinea',
            'GR' => 'Greece',
            'GT' => 'Guatemala',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HK' => 'Hong Kong',
            'HN' => 'Honduras',
            'HR' => 'Croatia',
            'HT' => 'Haiti',
            'HU' => 'Hungary',
            'ID' => 'Indonesia',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IN' => 'India',
            'IQ' => 'Iraq',
            'IS' => 'Iceland',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JO' => 'Jordan',
            'JP' => 'Japan',
            'KE' => 'Kenya',
            'KG' => 'Kyrgyzstan',
            'KH' => 'Cambodia',
            'KI' => 'Kiribati',
            'KM' => 'Comoros',
            'KN' => 'Saint Kitts and Nevis',
            'KR' => 'South Korea',
            'KW' => 'Kuwait',
            'KY' => 'Cayman Islands',
            'KZ' => 'Kazakhstan',
            'LA' => 'Laos',
            'LB' => 'Lebanon',
            'LC' => 'Saint Lucia',
            'LI' => 'Liechtenstein',
            'LK' => 'Sri Lanka',
            'LR' => 'Liberia',
            'LS' => 'Lesotho',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'LV' => 'Latvia',
            'LY' => 'Libya',
            'MA' => 'Morocco',
            'MC' => 'Monaco',
            'MD' => 'Moldova',
            'ME' => 'Montenegro',
            'MG' => 'Madagascar',
            'MH' => 'Marshall Islands',
            'MK' => 'North Macedonia',
            'ML' => 'Mali',
            'MM' => 'Myanmar',
            'MN' => 'Mongolia',
            'MO' => 'Macao',
            'MR' => 'Mauritania',
            'MS' => 'Montserrat',
            'MT' => 'Malta',
            'MU' => 'Mauritius',
            'MV' => 'Maldives',
            'MW' => 'Malawi',
            'MX' => 'Mexico',
            'MY' => 'Malaysia',
            'MZ' => 'Mozambique',
            'NA' => 'Namibia',
            'NC' => 'New Caledonia',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NI' => 'Nicaragua',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'NP' => 'Nepal',
            'NR' => 'Nauru',
            'NZ' => 'New Zealand',
            'OM' => 'Oman',
            'PA' => 'Panama',
            'PE' => 'Peru',
            'PG' => 'Papua New Guinea',
            'PH' => 'Philippines',
            'PK' => 'Pakistan',
            'PL' => 'Poland',
            'PS' => 'Palestine',
            'PT' => 'Portugal',
            'PW' => 'Palau',
            'PY' => 'Paraguay',
            'QA' => 'Qatar',
            'RO' => 'Romania',
            'RS' => 'Serbia',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'SA' => 'Saudi Arabia',
            'SB' => 'Solomon Islands',
            'SC' => 'Seychelles',
            'SD' => 'Sudan',
            'SE' => 'Sweden',
            'SG' => 'Singapore',
            'SI' => 'Slovenia',
            'SK' => 'Slovakia',
            'SL' => 'Sierra Leone',
            'SM' => 'San Marino',
            'SN' => 'Senegal',
            'SO' => 'Somalia',
            'SR' => 'Suriname',
            'ST' => 'São Tomé and Príncipe',
            'SV' => 'El Salvador',
            'SY' => 'Syria',
            'SZ' => 'Eswatini',
            'TD' => 'Chad',
            'TG' => 'Togo',
            'TH' => 'Thailand',
            'TJ' => 'Tajikistan',
            'TL' => 'Timor-Leste',
            'TM' => 'Turkmenistan',
            'TN' => 'Tunisia',
            'TO' => 'Tonga',
            'TR' => 'Turkey',
            'TT' => 'Trinidad and Tobago',
            'TV' => 'Tuvalu',
            'TW' => 'Taiwan',
            'TZ' => 'Tanzania',
            'UA' => 'Ukraine',
            'UG' => 'Uganda',
            'US' => 'United States',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VC' => 'Saint Vincent and the Grenadines',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'VU' => 'Vanuatu',
            'WS' => 'Samoa',
            'YE' => 'Yemen',
            'ZA' => 'South Africa',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        ];

        // Sort by country name for better UX
        asort($countries);
        
        return $countries;
    }
}