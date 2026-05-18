<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'variation_id', 'quantity', 'unit_cost', 'total_cost', 'selling_price',
    ];

    public function purchase()  { return $this->belongsTo(Purchase::class); }
    public function product()   { return $this->belongsTo(Product::class); }
    public function variation() { return $this->belongsTo(ProductVariation::class, 'variation_id'); }
}
