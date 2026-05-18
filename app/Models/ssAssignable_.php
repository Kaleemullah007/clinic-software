<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignable extends Model
{
    use HasFactory;

    protected $table = 'assign';
    public function assignable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function posts()
    {
        return $this->morphedByMany(User::class, 'assignable');
    }

    public function videos()
    {
        return $this->morphedByMany(Module::class, 'assignable');
    }

    public function Permisions()
    {
        return $this->morphedByMany(Permission::class, 'assignable');
    }



}

