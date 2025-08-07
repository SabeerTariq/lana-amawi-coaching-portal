<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'sender_type',
        'is_read',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include client messages.
     */
    public function scopeFromClient($query)
    {
        return $query->where('sender_type', 'client');
    }

    /**
     * Scope a query to only include admin messages.
     */
    public function scopeFromAdmin($query)
    {
        return $query->where('sender_type', 'admin');
    }

    /**
     * Check if the message has an attachment.
     */
    public function hasAttachment()
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get the attachment URL.
     */
    public function getAttachmentUrl()
    {
        if ($this->hasAttachment()) {
            return asset('storage/' . $this->attachment_path);
        }
        return null;
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSize()
    {
        if (!$this->attachment_size) {
            return null;
        }

        $bytes = $this->attachment_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the read receipt status for display.
     */
    public function getReadReceiptStatus()
    {
        if ($this->sender_type === 'client') {
            // For client messages, show read status
            return $this->is_read ? 'read' : 'sent';
        } else {
            // For admin messages, always show as read (since client can see admin messages)
            return 'read';
        }
    }

    /**
     * Get the read receipt icon.
     */
    public function getReadReceiptIcon()
    {
        $status = $this->getReadReceiptStatus();
        
        switch ($status) {
            case 'read':
                return 'fas fa-check-double';
            case 'sent':
                return 'fas fa-check';
            default:
                return 'fas fa-check';
        }
    }

    /**
     * Get the file icon based on file type.
     */
    public function getFileIcon()
    {
        if (!$this->attachment_type) {
            return 'fas fa-file';
        }

        $type = strtolower($this->attachment_type);
        
        if (str_contains($type, 'image')) {
            return 'fas fa-image';
        } elseif (str_contains($type, 'pdf')) {
            return 'fas fa-file-pdf';
        } elseif (str_contains($type, 'word') || str_contains($type, 'document')) {
            return 'fas fa-file-word';
        } elseif (str_contains($type, 'excel') || str_contains($type, 'spreadsheet')) {
            return 'fas fa-file-excel';
        } elseif (str_contains($type, 'powerpoint') || str_contains($type, 'presentation')) {
            return 'fas fa-file-powerpoint';
        } elseif (str_contains($type, 'zip') || str_contains($type, 'archive')) {
            return 'fas fa-file-archive';
        } elseif (str_contains($type, 'text')) {
            return 'fas fa-file-alt';
        } else {
            return 'fas fa-file';
        }
    }
} 