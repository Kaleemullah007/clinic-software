<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'company', 'phone', 'email', 'address', 'status', 'notes',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
