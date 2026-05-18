<?php
namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast\Object_;

trait UserClinicTrait {
    public function UserClinics()
    {
        return auth()->access();
    }
}
