<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $with = ['appointmentService'];

    protected $guarded = ['id'];

    public function getTimeAttribute($value)
    {
        return date('H:i', strtotime($value));
    }

    // ── Existing ────────────────────────────────────────────────────────────

    public function appointmentService()
    {
        return $this->hasMany(AppointmentService::class);
    }

    /** Alias kept for backward compat */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ── New relationships ────────────────────────────────────────────────────

    /** Patient (same as customer, cleaner name used in new modules) */
    public function patient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Attending doctor */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /** Products / services dispensed at this appointment */
    public function products()
    {
        return $this->hasMany(AppointmentProduct::class);
    }

    /** Call logs linked to this appointment */
    public function callLogs()
    {
        return $this->hasMany(AppointmentCallLog::class);
    }

    /** Consent forms for this appointment */
    public function consentForms()
    {
        return $this->hasMany(ConsentForm::class);
    }

    /** Before/after photos for this appointment */
    public function beforeAfterPhotos()
    {
        return $this->hasMany(BeforeAfterPhoto::class);
    }

    /** Returns / refunds on products from this appointment */
    public function returns()
    {
        return $this->hasMany(AppointmentReturn::class);
    }
}
