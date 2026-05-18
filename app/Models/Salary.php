<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'user_id', 'month', 'year', 'basic_salary', 'bonus', 'deductions',
        'net_salary', 'notes', 'status', 'paid_date', 'payment_method', 'processed_by',
    ];

    public function user()        { return $this->belongsTo(User::class); }
    public function processedBy() { return $this->belongsTo(User::class, 'processed_by'); }

    public function getMonthNameAttribute(): string
    {
        return \Carbon\Carbon::create($this->year, $this->month)->format('F Y');
    }
}
