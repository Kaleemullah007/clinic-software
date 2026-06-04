<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'price', 'has_variations', 'track_inventory', 'category_id', 'status',
    ];

    protected $casts = [
        'has_variations'  => 'boolean',
        'track_inventory' => 'boolean',
    ];

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function appointmentProducts()
    {
        return $this->hasMany(AppointmentProduct::class);
    }

    public function posItems()
    {
        return $this->hasMany(PosOrderItem::class);
    }

    public function getStockAttribute(): float
    {
        return $this->inventory?->quantity ?? 0;
    }
}
