<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentService extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    // protected $fillable = ['name','appointment_id','price','discounted_price','discount','service_id'];
}
