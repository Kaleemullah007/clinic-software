<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id', 'variation_id', 'type', 'quantity', 'unit_price',
        'reference_type', 'reference_id', 'appointment_code', 'vendor_id', 'created_by', 'notes',
    ];

    public function product()   { return $this->belongsTo(Product::class); }
    public function variation() { return $this->belongsTo(ProductVariation::class, 'variation_id'); }
    public function vendor()    { return $this->belongsTo(Vendor::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
}
