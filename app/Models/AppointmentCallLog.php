<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentCallLog extends Model
{
    protected $fillable = [
        'appointment_id', 'patient_id', 'called_by',
        'call_type', 'call_status', 'notes', 'call_at',
    ];

    protected $casts = ['call_at' => 'datetime'];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient()     { return $this->belongsTo(User::class, 'patient_id'); }
    public function calledBy()    { return $this->belongsTo(User::class, 'called_by'); }
}
