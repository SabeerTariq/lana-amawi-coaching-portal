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
    ];

    protected $casts = [
        'agreement_sent_at' => 'datetime',
        'agreement_uploaded_at' => 'datetime',
        'approved_at' => 'datetime',
        'payment_requested_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'amount_paid' => 'decimal:2',
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
}