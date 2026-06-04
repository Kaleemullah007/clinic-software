<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'remarks',
        'medicine',
        'dosage',
        'appointment_id',
        'user_id',
        'doctor_id',
        'status',
    ];

    public function appointment()
    {
        return $this->belongsTo(\App\Models\Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'doctor_id');
    }
}
