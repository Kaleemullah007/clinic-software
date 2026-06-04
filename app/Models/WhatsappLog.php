<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsappLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'sent_by',
        'phone',
        'status',
        'meta_message_id',
        'error_message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}
