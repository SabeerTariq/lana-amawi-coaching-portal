<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SlotException extends Model
{
    use HasFactory;

    protected $fillable = [
        'exception_date',
        'booking_type',
        'start_time',
        'end_time',
        'exception_type',
        'reason',
        'is_active'
    ];

    protected $casts = [
        'exception_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 'All Day';
        }
        
        return $this->start_time->format('g:i A') . ' - ' . $this->end_time->format('g:i A');
    }

    /**
     * Get formatted booking type
     */
    public function getBookingTypeFormattedAttribute()
    {
        return match($this->booking_type) {
            'in-office' => 'In-Office',
            'virtual' => 'Virtual',
            'both' => 'Both Types',
            default => $this->booking_type
        };
    }

    /**
     * Get formatted exception type
     */
    public function getExceptionTypeFormattedAttribute()
    {
        return match($this->exception_type) {
            'blocked' => 'Blocked',
            'modified' => 'Modified Hours',
            'closed' => 'Closed',
            default => $this->exception_type
        };
    }

    /**
     * Get badge color for exception type
     */
    public function getExceptionTypeBadgeColorAttribute()
    {
        return match($this->exception_type) {
            'blocked' => 'warning',
            'modified' => 'info',
            'closed' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if this exception affects a specific time
     */
    public function affectsTime($time)
    {
        // If no specific time range, affects entire day
        if (!$this->start_time || !$this->end_time) {
            return true;
        }
        
        $timeCarbon = Carbon::createFromFormat('H:i', $time);
        return $timeCarbon->gte($this->start_time) && $timeCarbon->lt($this->end_time);
    }

    /**
     * Check if this exception affects a specific booking type
     */
    public function affectsBookingType($type)
    {
        return $this->booking_type === 'both' || $this->booking_type === $type;
    }

    /**
     * Scope for active exceptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('exception_date', $date);
    }

    /**
     * Scope for specific booking type
     */
    public function scopeForBookingType($query, $type)
    {
        return $query->where(function($q) use ($type) {
            $q->where('booking_type', $type)
              ->orWhere('booking_type', 'both');
        });
    }

    /**
     * Scope for date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('exception_date', [$startDate, $endDate]);
    }
}
