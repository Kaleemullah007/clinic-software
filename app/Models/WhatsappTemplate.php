<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WhatsappTemplate extends Model
{
    protected $fillable = [
        'name', 'message_type', 'message_body', 'image_path', 'status', 'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaigns()
    {
        return $this->hasMany(WhatsappCampaign::class, 'template_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
