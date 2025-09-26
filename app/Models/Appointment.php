<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program',
        'appointment_date',
        'appointment_time',
        'booking_type',
        'message',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    /**
     * Get the user that owns the appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
     * Get the client name.
     */
    public function getClientNameAttribute()
    {
        return $this->user->name ?? 'Unknown Client';
    }

    /**
     * Scope for upcoming appointments (future date and not completed/cancelled)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
                     ->where('status', '!=', 'cancelled')
                     ->where('status', '!=', 'completed');
    }

    /**
     * Scope for past appointments (past date or completed)
     */
    public function scopePast($query)
    {
        return $query->where(function($q) {
            $q->where('appointment_date', '<', now()->toDateString())
              ->orWhere('status', 'completed');
        });
    }

    /**
     * Scope for completed appointments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if appointment is in the past
     */
    public function getIsPastAttribute()
    {
        return $this->appointment_date < now()->toDateString() || $this->status === 'completed';
    }

    /**
     * Check if appointment is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        return $this->appointment_date >= now()->toDateString() && 
               $this->status !== 'cancelled' && 
               $this->status !== 'completed';
    }

    /**
     * Check if appointment is completed
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if appointment is cancelled
     */
    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Convert 24-hour time to AM/PM format for display
     */
    public function getFormattedTimeAttribute()
    {
        if (!$this->appointment_time) {
            return '';
        }
        
        return date('g:i A', strtotime($this->appointment_time));
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
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update status for past appointments that are still confirmed
        static::updating(function ($appointment) {
            // If appointment is being marked as completed, ensure it's treated as past
            if ($appointment->status === 'completed') {
                // The appointment will now appear in past appointments due to our scope
            }
        });
    }

    /**
     * Scope for active appointments (not completed or cancelled)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope for today's appointments
     */
    public function scopeToday($query)
    {
        return $query->where('appointment_date', now()->toDateString());
    }
} 