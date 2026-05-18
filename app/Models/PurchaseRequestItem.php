<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    protected $fillable = ['purchase_request_id', 'product_id', 'variation_id', 'quantity', 'notes'];

    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }
    public function product()         { return $this->belongsTo(Product::class); }
    public function variation()       { return $this->belongsTo(ProductVariation::class, 'variation_id'); }
}
