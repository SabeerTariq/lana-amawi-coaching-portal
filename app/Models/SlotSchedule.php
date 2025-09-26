<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SlotSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'day_of_week',
        'booking_type',
        'start_time',
        'end_time',
        'slot_duration',
        'break_duration',
        'is_active',
        'max_bookings_per_slot',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
        'slot_duration' => 'integer',
        'break_duration' => 'integer',
        'max_bookings_per_slot' => 'integer'
    ];

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
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
            default => $this->booking_type
        };
    }

    /**
     * Generate time slots for this schedule
     */
    public function generateTimeSlots()
    {
        $slots = [];
        $current = $this->start_time->copy();
        
        while ($current->lt($this->end_time)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($this->slot_duration + $this->break_duration);
        }
        
        return $slots;
    }

    /**
     * Check if a specific time falls within this schedule
     */
    public function containsTime($time)
    {
        $timeCarbon = Carbon::createFromFormat('H:i', $time);
        return $timeCarbon->gte($this->start_time) && $timeCarbon->lt($this->end_time);
    }

    /**
     * Scope for active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific day
     */
    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    /**
     * Scope for specific booking type
     */
    public function scopeForBookingType($query, $type)
    {
        return $query->where('booking_type', $type);
    }
}
