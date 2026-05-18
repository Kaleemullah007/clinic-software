<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentProduct extends Model
{
    protected $fillable = [
        'appointment_id', 'product_id', 'variation_id', 'product_name', 'product_code',
        'quantity', 'unit_price', 'total_price', 'doctor_share_amount', 'clinic_share_amount',
        'deduct_inventory', 'notes', 'added_by',
    ];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function product()     { return $this->belongsTo(Product::class); }
    public function variation()   { return $this->belongsTo(ProductVariation::class, 'variation_id'); }
    public function addedBy()     { return $this->belongsTo(User::class, 'added_by'); }
    public function returns()     { return $this->hasMany(AppointmentReturn::class); }
}
