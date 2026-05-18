<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'category', 'clinic_id', 'amount', 'expense_date',
        'payment_method', 'reference_number', 'receipt_image', 'notes', 'created_by',
    ];

    public function clinic()    { return $this->belongsTo(Clinic::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
}
