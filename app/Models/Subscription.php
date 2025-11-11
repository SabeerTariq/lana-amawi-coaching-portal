<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'subscription_type',
        'monthly_price',
        'monthly_sessions',
        'is_active',
        'starts_at',
        'ends_at',
        'next_billing_date',
        'last_billing_date',
        'total_bookings_this_month',
        'subscription_features',
        'notes',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'last_billing_date' => 'datetime',
        'subscription_features' => 'array',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program for this subscription
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the bookings for this subscription
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'user_id')
                    ->where('created_at', '>=', $this->starts_at)
                    ->when($this->ends_at, function($query) {
                        return $query->where('created_at', '<=', $this->ends_at);
                    });
    }

    /**
     * Get bookings for current month
     */
    public function currentMonthBookings()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        return $this->bookings()
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
    }

    /**
     * Check if subscription has reached booking limit for current month
     */
    public function hasReachedBookingLimit()
    {
        return $this->currentMonthBookings()->count() >= $this->monthly_sessions;
    }

    /**
     * Get remaining bookings for current month
     */
    public function getRemainingBookingsAttribute()
    {
        $used = $this->currentMonthBookings()->count();
        return max(0, $this->monthly_sessions - $used);
    }

    /**
     * Check if subscription is active and not expired
     */
    public function isActive()
    {
        return $this->is_active && 
               (!$this->ends_at || $this->ends_at->isFuture());
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('ends_at')
                          ->orWhere('ends_at', '>', now());
                    });
    }

    /**
     * Get formatted monthly price
     */
    public function getFormattedMonthlyPriceAttribute()
    {
        return '$' . number_format($this->monthly_price, 2);
    }

    /**
     * Get subscription status
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        if ($this->ends_at && $this->ends_at->isPast()) {
            return 'expired';
        }
        
        return 'active';
    }

    /**
     * Get formatted subscription type display name
     */
    public function getFormattedSubscriptionTypeAttribute()
    {
        if (!$this->subscription_type) {
            return 'General';
        }

        return match($this->subscription_type) {
            'life_coaching' => 'Life Coaching',
            'student' => 'Student',
            'professional' => 'Professional',
            'relationship' => 'Relationship',
            'resident' => 'Resident',
            'fellow' => 'Fellow',
            'concierge' => 'Concierge',
            default => ucfirst(str_replace('_', ' ', $this->subscription_type))
        };
    }
}
