<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['country_id', 'name', 'code', 'is_active'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class)->orderBy('name');
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
