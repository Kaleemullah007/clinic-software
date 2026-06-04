<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceApproval extends Model
{
    protected $fillable = [
        'code', 'user_id', 'token', 'browser',
        'ip_address', 'status', 'actioned_by', 'actioned_at',
    ];

    protected $casts = [
        'actioned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }

    /** Parse the user-agent into a short human-readable label */
    public function getBrowserLabelAttribute(): string
    {
        $ua = $this->browser ?? '';
        if (str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg'))  return 'Chrome';
        if (str_contains($ua, 'Firefox'))  return 'Firefox';
        if (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) return 'Safari';
        if (str_contains($ua, 'Edg'))      return 'Edge';
        if (str_contains($ua, 'OPR') || str_contains($ua, 'Opera')) return 'Opera';
        return 'Browser';
    }

    /** OS label from user-agent */
    public function getOsLabelAttribute(): string
    {
        $ua = $this->browser ?? '';
        if (str_contains($ua, 'Windows NT')) return 'Windows';
        if (str_contains($ua, 'Macintosh'))  return 'macOS';
        if (str_contains($ua, 'Linux'))       return 'Linux';
        if (str_contains($ua, 'Android'))     return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        return 'Unknown OS';
    }
}
