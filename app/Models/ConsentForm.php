<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsentForm extends Model
{
    protected $fillable = [
        'appointment_id', 'patient_id', 'form_title', 'form_content',
        'signature_image', 'signed', 'signed_at', 'created_by',
    ];

    protected $dates = ['signed_at'];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient()     { return $this->belongsTo(User::class, 'patient_id'); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
}
