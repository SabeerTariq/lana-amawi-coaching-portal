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
     * Get the client name.
     */
    public function getClientNameAttribute()
    {
        return $this->user->name ?? 'Unknown Client';
    }
} 