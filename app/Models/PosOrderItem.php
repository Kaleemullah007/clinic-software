<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosOrderItem extends Model
{
    protected $fillable = [
        'pos_order_id', 'product_id', 'variation_id',
        'product_name', 'variation_name',
        'quantity', 'unit_price', 'line_total',
    ];

    protected $casts = [
        'unit_price' => 'float',
        'line_total' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}
