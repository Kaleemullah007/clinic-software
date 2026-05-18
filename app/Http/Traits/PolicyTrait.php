<?php
namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;


trait PolicyTrait {
    public function before(Model $Object)
    {

        return true;
    }
}
