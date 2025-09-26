<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'program',
        'preferred_date',
        'preferred_time',
        'booking_type',
        'message',
        'status',
        'admin_suggestion',
        'client_response',
        'response_date',
        'signed_agreement_path',
        'signed_agreement_name',
        'agreement_uploaded_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'response_date' => 'datetime',
        'agreement_uploaded_at' => 'datetime',
    ];

    // Status constants for better tracking
    const STATUS_PENDING = 'pending';
    const STATUS_SUGGESTED_ALTERNATIVE = 'suggested_alternative';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_MODIFIED = 'modified';
    const STATUS_CONVERTED = 'converted';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the program name.
     */
    public function getProgramNameAttribute()
    {
        if (!$this->program) {
            return 'General Coaching Session';
        }
        
        $programs = [
            'life_coaching' => 'Life Coaching Session',
            'career_coaching' => 'Career Coaching Session',
            'relationship_coaching' => 'Relationship Coaching Session',
            'wellness_coaching' => 'Wellness Coaching Session',
        ];

        return $programs[$this->program] ?? $this->program;
    }

    /**
     * Get the formatted booking type.
     */
    public function getBookingTypeFormattedAttribute()
    {
        return match($this->booking_type) {
            'in-office' => 'In-Office',
            'virtual' => 'Virtual',
            default => $this->booking_type
        };
    }

    /**
     * Check if booking is waiting for client response
     */
    public function isWaitingForResponse()
    {
        return $this->status === self::STATUS_SUGGESTED_ALTERNATIVE;
    }

    /**
     * Check if booking has been responded to
     */
    public function hasClientResponse()
    {
        return in_array($this->status, [
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_MODIFIED
        ]);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_SUGGESTED_ALTERNATIVE => 'info',
            self::STATUS_ACCEPTED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_MODIFIED => 'primary',
            self::STATUS_CONVERTED => 'secondary',
            self::STATUS_CANCELLED => 'dark',
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
            self::STATUS_SUGGESTED_ALTERNATIVE => 'Alternative Suggested',
            self::STATUS_ACCEPTED => 'Accepted by Client',
            self::STATUS_REJECTED => 'Rejected by Client',
            self::STATUS_MODIFIED => 'Modified by Client',
            self::STATUS_CONVERTED => 'Converted to Appointment',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    /**
     * Convert 24-hour time to AM/PM format for display
     */
    public function getFormattedTimeAttribute()
    {
        if (!$this->preferred_time) {
            return '';
        }
        
        return date('g:i A', strtotime($this->preferred_time));
    }

    /**
     * Static method to convert any time string to AM/PM format
     */
    public static function formatTime($time)
    {
        if (!$time) {
            return '';
        }
        
        return date('g:i A', strtotime($time));
    }

    /**
     * Check if the booking has a signed agreement uploaded
     */
    public function hasSignedAgreement()
    {
        return !empty($this->signed_agreement_path);
    }

    /**
     * Get the agreement upload status for display
     */
    public function getAgreementStatusAttribute()
    {
        if ($this->hasSignedAgreement()) {
            return 'uploaded';
        }
        return 'pending';
    }

    /**
     * Get the agreement status badge color
     */
    public function getAgreementStatusBadgeColorAttribute()
    {
        return $this->hasSignedAgreement() ? 'success' : 'warning';
    }

    /**
     * Get the agreement status display text
     */
    public function getAgreementStatusTextAttribute()
    {
        return $this->hasSignedAgreement() ? 'Agreement Uploaded' : 'Agreement Pending';
    }

    /**
     * Get the agreement file URL
     */
    public function getAgreementUrlAttribute()
    {
        if ($this->hasSignedAgreement()) {
            return Storage::url($this->signed_agreement_path);
        }
        return null;
    }
} 