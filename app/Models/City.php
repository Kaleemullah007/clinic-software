<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['state_id', 'name', 'is_active'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
