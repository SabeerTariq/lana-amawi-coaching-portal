<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'status',
        'admin_notes',
        'agreement_path',
        'signed_agreement_path',
        'signed_agreement_name',
        'agreement_sent_at',
        'agreement_uploaded_at',
        'approved_at',
        'payment_requested_at',
        'payment_completed_at',
        'amount_paid',
        'payment_reference',
        'contract_duration_months',
        'payment_type',
        'contract_start_date',
        'contract_end_date',
        'next_payment_date',
        'total_payments_due',
        'payments_completed',
        'one_time_payment_amount',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_price_id',
    ];

    protected $casts = [
        'agreement_sent_at' => 'datetime',
        'agreement_uploaded_at' => 'datetime',
        'approved_at' => 'datetime',
        'payment_requested_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'next_payment_date' => 'date',
        'one_time_payment_amount' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_AGREEMENT_SENT = 'agreement_sent';
    const STATUS_AGREEMENT_UPLOADED = 'agreement_uploaded';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAYMENT_REQUESTED = 'payment_requested';
    const STATUS_PAYMENT_COMPLETED = 'payment_completed';
    const STATUS_ACTIVE = 'active';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the user that owns the program selection
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get all payments for this user program
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get completed payments
     */
    public function completedPayments()
    {
        return $this->hasMany(Payment::class)->where('status', Payment::STATUS_COMPLETED);
    }

    /**
     * Check if agreement has been sent
     */
    public function hasAgreementSent()
    {
        return !is_null($this->agreement_sent_at);
    }

    /**
     * Check if signed agreement has been uploaded
     */
    public function hasSignedAgreement()
    {
        return !is_null($this->signed_agreement_path);
    }

    /**
     * Check if payment has been completed
     */
    public function hasPaymentCompleted()
    {
        return !is_null($this->payment_completed_at);
    }

    /**
     * Check if program is active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if program can be cancelled
     */
    public function canBeCancelled()
    {
        return !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_REJECTED]);
    }

    /**
     * Check if program can be re-selected (only cancelled programs)
     */
    public function canBeReselected()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_AGREEMENT_SENT => 'info',
            self::STATUS_AGREEMENT_UPLOADED => 'primary',
            self::STATUS_APPROVED => 'success',
            self::STATUS_PAYMENT_REQUESTED => 'warning',
            self::STATUS_PAYMENT_COMPLETED => 'success',
            self::STATUS_ACTIVE => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayTextAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_AGREEMENT_SENT => 'Agreement Sent',
            self::STATUS_AGREEMENT_UPLOADED => 'Agreement Uploaded',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PAYMENT_REQUESTED => 'Payment Requested',
            self::STATUS_PAYMENT_COMPLETED => 'Payment Completed',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get the agreement file URL
     */
    public function getAgreementUrlAttribute()
    {
        if ($this->agreement_path) {
            return Storage::url($this->agreement_path);
        }
        return null;
    }

    /**
     * Get the signed agreement file URL
     */
    public function getSignedAgreementUrlAttribute()
    {
        if ($this->signed_agreement_path) {
            return Storage::url($this->signed_agreement_path);
        }
        return null;
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountPaidAttribute()
    {
        return $this->amount_paid ? '$' . number_format($this->amount_paid, 2) : null;
    }

    /**
     * Payment type constants
     */
    const PAYMENT_TYPE_MONTHLY = 'monthly';
    const PAYMENT_TYPE_ONE_TIME = 'one_time';

    /**
     * Initialize contract when program is activated
     */
    public function initializeContract($paymentType = self::PAYMENT_TYPE_MONTHLY)
    {
        $startDate = now();
        $endDate = now()->addMonths($this->contract_duration_months ?? 3);
        $oneTimeAmount = $this->program->one_time_payment_amount ?? ($this->program->monthly_price ?? 0) * ($this->contract_duration_months ?? 3);

        // For one-time payments, total_payments_due should be 1, not the contract duration
        $totalPaymentsDue = $paymentType === self::PAYMENT_TYPE_ONE_TIME 
            ? 1 
            : ($this->contract_duration_months ?? 3);

        $this->update([
            'payment_type' => $paymentType,
            'contract_start_date' => $startDate,
            'contract_end_date' => $endDate,
            'total_payments_due' => $totalPaymentsDue,
            'next_payment_date' => $paymentType === self::PAYMENT_TYPE_MONTHLY ? $startDate : null,
            'one_time_payment_amount' => $paymentType === self::PAYMENT_TYPE_ONE_TIME ? $oneTimeAmount : null,
        ]);
    }

    /**
     * Check if contract is active
     */
    public function isContractActive()
    {
        if (!$this->contract_start_date || !$this->contract_end_date) {
            return false;
        }
        return now()->between($this->contract_start_date, $this->contract_end_date);
    }

    /**
     * Check if all contract payments are completed
     */
    public function areAllPaymentsCompleted()
    {
        return $this->payments_completed >= $this->total_payments_due;
    }

    /**
     * Get remaining payments count
     */
    public function getRemainingPaymentsAttribute()
    {
        return max(0, $this->total_payments_due - $this->payments_completed);
    }

    /**
     * Get total contract amount
     */
    public function getTotalContractAmountAttribute()
    {
        $monthlyPrice = $this->program->monthly_price ?? 0;
        return $monthlyPrice * ($this->contract_duration_months ?? 3);
    }

    /**
     * Get monthly payment amount
     */
    public function getMonthlyPaymentAmountAttribute()
    {
        return $this->program->monthly_price ?? 0;
    }

    /**
     * Check if next payment is due
     */
    public function isNextPaymentDue()
    {
        if ($this->payment_type !== self::PAYMENT_TYPE_MONTHLY) {
            return false;
        }
        return $this->next_payment_date && now()->greaterThanOrEqualTo($this->next_payment_date) && !$this->areAllPaymentsCompleted();
    }

    /**
     * Get current month bookings count
     */
    public function getCurrentMonthBookingsCount()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        return Appointment::where('user_id', $this->user_id)
            ->where('program', $this->program->name ?? '')
            ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    /**
     * Check if user has reached monthly booking limit
     */
    public function hasReachedMonthlyLimit()
    {
        $monthlySessions = $this->program->monthly_sessions ?? 0;
        return $this->getCurrentMonthBookingsCount() >= $monthlySessions;
    }

    /**
     * Get remaining bookings for current month
     */
    public function getRemainingBookingsAttribute()
    {
        $monthlySessions = $this->program->monthly_sessions ?? 0;
        $used = $this->getCurrentMonthBookingsCount();
        return max(0, $monthlySessions - $used);
    }

    /**
     * Get additional booking charge
     */
    public function getAdditionalBookingChargeAttribute()
    {
        return $this->program->additional_booking_charge ?? 0;
    }
}