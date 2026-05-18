<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedProduct extends Model
{
    protected $fillable = [
        'product_id', 'variation_id', 'quantity', 'cost_value',
        'reason', 'reference_type', 'reference_id', 'reported_by',
    ];

    public function product()    { return $this->belongsTo(Product::class); }
    public function variation()  { return $this->belongsTo(ProductVariation::class, 'variation_id'); }
    public function reportedBy() { return $this->belongsTo(User::class, 'reported_by'); }
}
