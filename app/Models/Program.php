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
        'duration_weeks',
        'sessions_included',
        'is_active',
        'features',
        'subscription_type',
        'monthly_price',
        'monthly_sessions',
        'booking_limit_per_month',
        'is_subscription_based',
        'subscription_features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'monthly_price' => 'decimal:2',
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
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get program duration text
     */
    public function getDurationTextAttribute()
    {
        if ($this->duration_weeks) {
            return $this->duration_weeks . ' week' . ($this->duration_weeks > 1 ? 's' : '');
        }
        return null;
    }

    /**
     * Get sessions text
     */
    public function getSessionsTextAttribute()
    {
        if ($this->sessions_included) {
            return $this->sessions_included . ' session' . ($this->sessions_included > 1 ? 's' : '') . ' included';
        }
        return null;
    }
}