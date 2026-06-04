<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappCampaign extends Model
{
    protected $fillable = [
        'name', 'template_id', 'target_role', 'clinic_id', 'doctor_id',
        'scheduled_at', 'timezone', 'message_delay', 'status',
        'total_recipients', 'sent_count', 'pending_count', 'failed_count', 'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'template_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(WhatsappCampaignLog::class, 'campaign_id');
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_recipients === 0) return 0;
        return (int) round(($this->sent_count + $this->failed_count) / $this->total_recipients * 100);
    }
}
