<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use SoftDeletes;

    protected $fillable = ['pr_number', 'requested_by', 'approved_by', 'status', 'notes', 'approved_at'];
    protected $dates = ['approved_at'];

    public function requestedBy()  { return $this->belongsTo(User::class, 'requested_by'); }
    public function approvedBy()   { return $this->belongsTo(User::class, 'approved_by'); }
    public function items()        { return $this->hasMany(PurchaseRequestItem::class); }
    public function purchases()    { return $this->hasMany(Purchase::class); }

    public static function generateNumber(): string
    {
        $last = static::withTrashed()->latest('id')->value('id') ?? 0;
        return 'PR-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
