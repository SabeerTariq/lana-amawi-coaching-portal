<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'message',
        'status',
        'admin_suggestion',
        'client_response',
        'response_date',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'response_date' => 'datetime',
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
} 