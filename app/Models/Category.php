<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        // 'url',
        // 'slug',
        'status',
        // 'is_parent'
    ];

    public function ParentCategory(){
        return $this->belongsTo('App\Models\Category','is_parent','id');
    }

    /** Count of appointment_services that use this category */
    public function appointmentServices()
    {
        return $this->hasMany(\App\Models\AppointmentService::class, 'service_id');
    }
}
