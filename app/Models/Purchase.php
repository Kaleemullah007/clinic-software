<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_number', 'vendor_id', 'purchase_request_id', 'created_by',
        'purchase_date', 'total_amount', 'discount', 'net_amount',
        'payment_status', 'paid_amount', 'notes',
    ];

    public function vendor()          { return $this->belongsTo(Vendor::class); }
    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }
    public function creator()         { return $this->belongsTo(User::class, 'created_by'); }
    public function items()           { return $this->hasMany(PurchaseItem::class); }

    public static function generateNumber(): string
    {
        $last = static::withTrashed()->latest('id')->value('id') ?? 0;
        return 'PO-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
