<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserProgram;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event: ' . $event->type);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle successful payment intent
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded: ' . $paymentIntent->id);
        
        // Find payment by payment intent ID
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        
        if ($payment && $payment->status !== Payment::STATUS_COMPLETED) {
            $payment->update([
                'status' => Payment::STATUS_COMPLETED,
                'paid_at' => now(),
            ]);
        }
    }

    /**
     * Handle failed payment intent
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        Log::warning('Payment intent failed: ' . $paymentIntent->id);
        
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        
        if ($payment) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
            ]);
        }
    }

    /**
     * Handle successful invoice payment (for subscriptions)
     */
    private function handleInvoicePaymentSucceeded($invoice)
    {
        Log::info('Invoice payment succeeded: ' . $invoice->id);
        
        if (!$invoice->subscription) {
            return;
        }

        $userProgram = UserProgram::where('stripe_subscription_id', $invoice->subscription)->first();
        
        if ($userProgram) {
            // Create payment record for this month
            $amount = $invoice->amount_paid / 100; // Convert from cents
            $monthNumber = $userProgram->payments_completed + 1;
            
            Payment::create([
                'user_program_id' => $userProgram->id,
                'payment_type' => Payment::TYPE_CONTRACT_MONTHLY,
                'status' => Payment::STATUS_COMPLETED,
                'amount' => $amount,
                'payment_reference' => 'INV-' . $invoice->id,
                'month_number' => $monthNumber,
                'paid_at' => now(),
                'stripe_payment_intent_id' => $invoice->payment_intent,
                'stripe_customer_id' => $userProgram->stripe_customer_id,
                'notes' => 'Monthly subscription payment via Stripe. Invoice: ' . $invoice->id,
            ]);

            // Update user program
            $userProgram->increment('payments_completed');
            
            if ($userProgram->payments_completed >= $userProgram->total_payments_due) {
                // All payments completed
                $userProgram->update(['next_payment_date' => null]);
            } else {
                // Set next payment date
                $userProgram->update(['next_payment_date' => now()->addMonth()]);
            }
        }
    }

    /**
     * Handle failed invoice payment
     */
    private function handleInvoicePaymentFailed($invoice)
    {
        Log::warning('Invoice payment failed: ' . $invoice->id);
        
        if (!$invoice->subscription) {
            return;
        }

        $userProgram = UserProgram::where('stripe_subscription_id', $invoice->subscription)->first();
        
        if ($userProgram) {
            // Log failed payment
            $adminNotes = $userProgram->admin_notes ?? '';
            $adminNotes .= "\n\n[PAYMENT FAILED]\n";
            $adminNotes .= "Invoice: " . $invoice->id . "\n";
            $adminNotes .= "Failed: " . now()->format('Y-m-d H:i:s') . "\n";
            $adminNotes .= "Reason: " . ($invoice->last_payment_error->message ?? 'Unknown') . "\n";
            
            $userProgram->update(['admin_notes' => $adminNotes]);
        }
    }

    /**
     * Handle subscription deletion
     */
    private function handleSubscriptionDeleted($subscription)
    {
        Log::info('Subscription deleted: ' . $subscription->id);
        
        $userProgram = UserProgram::where('stripe_subscription_id', $subscription->id)->first();
        
        if ($userProgram) {
            $adminNotes = $userProgram->admin_notes ?? '';
            $adminNotes .= "\n\n[SUBSCRIPTION CANCELLED]\n";
            $adminNotes .= "Subscription: " . $subscription->id . "\n";
            $adminNotes .= "Cancelled: " . now()->format('Y-m-d H:i:s') . "\n";
            
            $userProgram->update([
                'admin_notes' => $adminNotes,
                'stripe_subscription_id' => null,
            ]);
        }
    }
}
