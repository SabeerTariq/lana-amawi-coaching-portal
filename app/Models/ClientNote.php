<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'note',
    ];

    /**
     * Get the client this note belongs to.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin who created this note.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
