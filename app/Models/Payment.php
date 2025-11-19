<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_program_id',
        'appointment_id',
        'payment_type',
        'status',
        'amount',
        'payment_reference',
        'notes',
        'month_number',
        'paid_at',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_customer_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Payment types
    const TYPE_CONTRACT_MONTHLY = 'contract_monthly';
    const TYPE_CONTRACT_ONE_TIME = 'contract_one_time';
    const TYPE_ADDITIONAL_SESSION = 'additional_session';

    // Payment statuses
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Get the user program this payment belongs to
     */
    public function userProgram()
    {
        return $this->belongsTo(UserProgram::class);
    }

    /**
     * Get the appointment this payment is for (if additional session)
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get payment type display text
     */
    public function getPaymentTypeDisplayAttribute()
    {
        return match($this->payment_type) {
            self::TYPE_CONTRACT_MONTHLY => 'Monthly Contract Payment',
            self::TYPE_CONTRACT_ONE_TIME => 'One-Time Contract Payment',
            self::TYPE_ADDITIONAL_SESSION => 'Additional Session Payment',
            default => ucfirst(str_replace('_', ' ', $this->payment_type))
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_REFUNDED => 'secondary',
            default => 'secondary'
        };
    }
}
