<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeforeAfterPhoto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'appointment_id', 'patient_id', 'photo_type', 'file_path', 'caption', 'patient_consent', 'uploaded_by',
    ];

    public function appointment()  { return $this->belongsTo(Appointment::class); }
    public function patient()      { return $this->belongsTo(User::class, 'patient_id'); }
    public function uploadedBy()   { return $this->belongsTo(User::class, 'uploaded_by'); }
}
