<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Assign extends Model
{
    use HasFactory;

    // protected $table = 'assign';
    public function assignable()
    {
        return $this->morphTo();
    }
}
