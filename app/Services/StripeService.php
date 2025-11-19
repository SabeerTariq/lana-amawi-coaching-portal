<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Invoice;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    /**
     * Initialize Stripe with API key from database or config
     * Always fetches fresh values to ensure latest settings are used
     */
    protected function initializeStripe()
    {
        // Always get fresh values from database (not cached)
        $stripeSecret = \App\Models\Setting::get('stripe_secret', config('services.stripe.secret'));
        Stripe::setApiKey($stripeSecret);
    }

    public function __construct()
    {
        $this->initializeStripe();
    }

    /**
     * Get current Stripe publishable key (always fresh from database)
     */
    public function getPublishableKey()
    {
        return \App\Models\Setting::get('stripe_key', config('services.stripe.key'));
    }

    /**
     * Get current Stripe secret key (always fresh from database)
     */
    public function getSecretKey()
    {
        return \App\Models\Setting::get('stripe_secret', config('services.stripe.secret'));
    }

    /**
     * Refresh Stripe API key (useful after settings update)
     */
    public function refreshApiKey()
    {
        $this->initializeStripe();
    }

    /**
     * Ensure Stripe is initialized with latest API key before any operation
     */
    protected function ensureInitialized()
    {
        // Re-initialize to get latest API key from database
        $this->initializeStripe();
    }

    /**
     * Create a Stripe customer
     */
    public function createCustomer($email, $name, $metadata = [])
    {
        $this->ensureInitialized();
        try {
            return Customer::create([
                'email' => $email,
                'name' => $name,
                'metadata' => $metadata,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe customer creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a payment intent for one-time payment
     */
    public function createPaymentIntent($amount, $currency = 'usd', $customerId = null, $metadata = [], $description = null)
    {
        $this->ensureInitialized();
        try {
            $params = [
                'amount' => $amount * 100, // Convert to cents
                'currency' => $currency,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => $metadata,
            ];

            if ($customerId) {
                $params['customer'] = $customerId;
            }

            if ($description) {
                $params['description'] = $description;
            }

            return PaymentIntent::create($params);
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a subscription for monthly payments
     */
    public function createSubscription($customerId, $priceId, $metadata = [])
    {
        $this->ensureInitialized();
        try {
            $subscription = Subscription::create([
                'customer' => $customerId,
                'items' => [
                    ['price' => $priceId],
                ],
                'metadata' => $metadata,
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                    'payment_method_types' => ['card'],
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);
            
            Log::info('Subscription created, checking invoice', [
                'subscription_id' => $subscription->id,
                'latest_invoice' => is_string($subscription->latest_invoice) 
                    ? $subscription->latest_invoice 
                    : ($subscription->latest_invoice->id ?? 'unknown'),
            ]);
            
            // Ensure the invoice has a payment intent
            if ($subscription->latest_invoice) {
                $invoiceId = is_string($subscription->latest_invoice) 
                    ? $subscription->latest_invoice 
                    : $subscription->latest_invoice->id;
                
                if ($invoiceId) {
                    $invoice = Invoice::retrieve($invoiceId, ['expand' => ['payment_intent']]);
                    
                    Log::info('Invoice status check', [
                        'invoice_id' => $invoice->id,
                        'invoice_status' => $invoice->status,
                        'has_payment_intent' => $invoice->payment_intent ? 'yes' : 'no',
                    ]);
                    
                    // If invoice is draft, finalize it to create payment intent
                    if ($invoice->status === 'draft') {
                        Log::info('Finalizing draft invoice');
                        $invoice = $invoice->finalizeInvoice(['expand' => ['payment_intent']]);
                        
                        // Re-retrieve to ensure we have the latest state
                        $invoice = Invoice::retrieve($invoice->id, ['expand' => ['payment_intent']]);
                        
                        Log::info('Invoice finalized', [
                            'invoice_id' => $invoice->id,
                            'invoice_status' => $invoice->status,
                            'has_payment_intent' => $invoice->payment_intent ? 'yes' : 'no',
                        ]);
                        
                        // Update subscription's latest_invoice reference
                        $subscription->latest_invoice = $invoice;
                    }
                    
                    // If still no payment_intent, try to pay the invoice which will create one
                    if (!$invoice->payment_intent && $invoice->status !== 'paid') {
                        Log::warning('Invoice has no payment_intent, attempting to create one');
                        // The payment_intent should be created when we try to pay, but since we're using
                        // default_incomplete, we need to wait for the client to provide payment method
                        // The payment_intent will be created when the invoice is finalized
                    }
                }
            }
            
            return $subscription;
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription creation failed: ' . $e->getMessage());
            Log::error('Stripe error details: ' . json_encode($e->getJsonBody()));
            throw $e;
        }
    }

    /**
     * Create a price for a product (for subscriptions)
     */
    public function createPrice($productId, $amount, $currency = 'usd', $interval = 'month')
    {
        $this->ensureInitialized();
        try {
            return \Stripe\Price::create([
                'product' => $productId,
                'unit_amount' => $amount * 100, // Convert to cents
                'currency' => $currency,
                'recurring' => [
                    'interval' => $interval,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe price creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a product
     */
    public function createProduct($name, $description = null, $metadata = [])
    {
        $this->ensureInitialized();
        try {
            return \Stripe\Product::create([
                'name' => $name,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe product creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve a payment intent
     */
    public function retrievePaymentIntent($paymentIntentId, $expand = [])
    {
        $this->ensureInitialized();
        try {
            $params = [];
            if (!empty($expand)) {
                $params['expand'] = $expand;
            }
            return PaymentIntent::retrieve($paymentIntentId, $params);
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent retrieval failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve a subscription
     */
    public function retrieveSubscription($subscriptionId)
    {
        $this->ensureInitialized();
        try {
            return Subscription::retrieve($subscriptionId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription retrieval failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription($subscriptionId)
    {
        $this->ensureInitialized();
        try {
            $subscription = Subscription::retrieve($subscriptionId);
            return $subscription->cancel();
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription cancellation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a setup intent for saving payment method
     */
    public function createSetupIntent($customerId, $metadata = [])
    {
        $this->ensureInitialized();
        try {
            return \Stripe\SetupIntent::create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'metadata' => $metadata,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe setup intent creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}

