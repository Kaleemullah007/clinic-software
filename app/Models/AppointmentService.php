<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;
use App\Models\Category;

class AppointmentService extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /** The appointment this service line belongs to */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /** The category (service type) — service_id references categories.id */
    public function category()
    {
        return $this->belongsTo(Category::class, 'service_id');
    }
}
