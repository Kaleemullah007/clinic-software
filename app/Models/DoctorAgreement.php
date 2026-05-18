<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorAgreement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'doctor_id', 'clinic_id', 'service_id', 'share_type',
        'doctor_share', 'clinic_share', 'effective_from', 'effective_to', 'is_active', 'notes', 'created_by',
    ];

    public function doctor()  { return $this->belongsTo(User::class, 'doctor_id'); }
    public function clinic()  { return $this->belongsTo(Clinic::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    /**
     * Get the active agreement for a doctor (optionally scoped to service/clinic).
     */
    public static function activeFor(int $doctorId, ?int $serviceId = null, ?int $clinicId = null): ?self
    {
        return static::where('doctor_id', $doctorId)
            ->where('is_active', true)
            ->when($serviceId, fn($q) => $q->where(fn($q2) => $q2->whereNull('service_id')->orWhere('service_id', $serviceId)))
            ->when($clinicId,  fn($q) => $q->where(fn($q2) => $q2->whereNull('clinic_id')->orWhere('clinic_id', $clinicId)))
            ->where('effective_from', '<=', now())
            ->where(fn($q) => $q->whereNull('effective_to')->orWhere('effective_to', '>=', now()))
            ->orderByDesc('effective_from')
            ->first();
    }
}
