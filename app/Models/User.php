<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'age',
        'languages_spoken',
        'institution_hospital',
        'position',
        'position_as_of_date',
        'specialty',
        'education_institution',
        'graduation_date',
        'password',
        'is_admin',
        'signed_agreement_path',
        'signed_agreement_name',
        'agreement_uploaded_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'date_of_birth' => 'date',
            'position_as_of_date' => 'date',
            'graduation_date' => 'date',
            'languages_spoken' => 'array',
            'agreement_uploaded_at' => 'datetime',
        ];
    }

    /**
     * Get the user's appointments.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the user's messages.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the user's bookings (by email match).
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'email', 'email');
    }

    /**
     * Get the admin notes for this client.
     */
    public function notes()
    {
        return $this->hasMany(ClientNote::class);
    }

    /**
     * Get the user's program selections
     */
    public function userPrograms()
    {
        return $this->hasMany(UserProgram::class);
    }

    /**
     * Get the user's programs through user_programs
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'user_programs')
                    ->withPivot(['status', 'admin_notes', 'agreement_path', 'signed_agreement_path', 'signed_agreement_name', 'agreement_sent_at', 'agreement_uploaded_at', 'approved_at', 'payment_requested_at', 'payment_completed_at', 'amount_paid', 'payment_reference'])
                    ->withTimestamps();
    }

    /**
     * Check if user has uploaded a signed agreement.
     */
    public function hasSignedAgreement()
    {
        return !empty($this->signed_agreement_path);
    }

    /**
     * Get the agreement status text.
     */
    public function getAgreementStatusTextAttribute()
    {
        return $this->hasSignedAgreement() ? 'Signed' : 'Not Uploaded';
    }

    /**
     * Get the agreement status badge color.
     */
    public function getAgreementStatusBadgeColorAttribute()
    {
        return $this->hasSignedAgreement() ? 'success' : 'warning';
    }

    /**
     * Get the agreement file URL.
     */
    public function getAgreementUrlAttribute()
    {
        return $this->hasSignedAgreement() ? Storage::url($this->signed_agreement_path) : null;
    }
}
