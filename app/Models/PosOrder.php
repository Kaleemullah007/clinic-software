<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrder extends Model
{
    protected $fillable = [
        'order_number', 'clinic_id', 'user_id',
        'subtotal', 'discount', 'tax_label', 'tax_rate', 'tax_amount', 'grand_total',
        'shipping_address', 'payment_status', 'notes', 'created_by',
        'order_type', 'delivery_address',
    ];

    protected $casts = [
        'subtotal'    => 'float',
        'discount'    => 'float',
        'tax_rate'    => 'float',
        'tax_amount'  => 'float',
        'grand_total' => 'float',
    ];

    /* ── Relationships ─────────────────────────────────────────────── */

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosOrderItem::class);
    }

    /* ── Order number generator ────────────────────────────────────── */

    public static function generateOrderNumber(): string
    {
        $prefix = 'POS-' . now()->format('Ym') . '-';
        $last   = static::where('order_number', 'like', $prefix . '%')
                        ->orderByDesc('id')
                        ->value('order_number');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
