<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_months',
        'sessions_included',
        'is_active',
        'features',
        'subscription_type',
        'monthly_price',
        'monthly_sessions',
        'is_subscription_based',
        'subscription_features',
        'additional_booking_charge',
        'one_time_payment_amount',
        'agreement_template_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'monthly_price' => 'decimal:2',
        'additional_booking_charge' => 'decimal:2',
        'one_time_payment_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_subscription_based' => 'boolean',
        'features' => 'array',
        'subscription_features' => 'array',
    ];

    /**
     * Get the users who have selected this program
     */
    public function userPrograms()
    {
        return $this->hasMany(UserProgram::class);
    }

    /**
     * Get the subscriptions for this program
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get active subscriptions for this program
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(Subscription::class)->where('is_active', true);
    }

    /**
     * Get active programs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted price (legacy - kept for backward compatibility)
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get formatted monthly price
     */
    public function getFormattedMonthlyPriceAttribute()
    {
        return '$' . number_format($this->monthly_price ?? 0, 2);
    }

    /**
     * Get program duration text (legacy - kept for backward compatibility)
     */
    public function getDurationTextAttribute()
    {
        if ($this->duration_months) {
            return $this->duration_months . ' month' . ($this->duration_months > 1 ? 's' : '');
        }
        return null;
    }

    /**
     * Get sessions text (legacy - kept for backward compatibility)
     */
    public function getSessionsTextAttribute()
    {
        if ($this->sessions_included) {
            return $this->sessions_included . ' session' . ($this->sessions_included > 1 ? 's' : '') . ' included';
        }
        return null;
    }

    /**
     * Get formatted additional booking charge
     */
    public function getFormattedAdditionalBookingChargeAttribute()
    {
        return $this->additional_booking_charge ? '$' . number_format($this->additional_booking_charge, 2) : '$0.00';
    }

    /**
     * Get formatted one-time payment amount
     */
    public function getFormattedOneTimePaymentAmountAttribute()
    {
        return $this->one_time_payment_amount ? '$' . number_format($this->one_time_payment_amount, 2) : null;
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