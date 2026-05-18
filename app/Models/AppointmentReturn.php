<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentReturn extends Model
{
    protected $fillable = [
        'appointment_id', 'appointment_product_id', 'product_id', 'variation_id',
        'quantity', 'refund_amount', 'return_to', 'reason', 'processed_by',
    ];

    public function appointment()        { return $this->belongsTo(Appointment::class); }
    public function appointmentProduct() { return $this->belongsTo(AppointmentProduct::class); }
    public function product()            { return $this->belongsTo(Product::class); }
    public function processedBy()        { return $this->belongsTo(User::class, 'processed_by'); }
}
