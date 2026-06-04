<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappCampaignLog extends Model
{
    protected $fillable = [
        'campaign_id', 'user_id', 'recipient_name', 'phone',
        'status', 'meta_message_id', 'error_message', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(WhatsappCampaign::class, 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeSent($q)    { return $q->where('status', 'sent'); }
    public function scopeFailed($q)  { return $q->where('status', 'failed'); }
}
