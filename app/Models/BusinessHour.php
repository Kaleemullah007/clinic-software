<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    use HasFactory;
    public function getTimeSlotsAttribute(){

        $times = CarbonInterval::minutes($this->step)->toPeriod($this->from,$this->to)->toArray();
        return array_map(fn($time)=>
        $time->format('H:i'),$times);
    }
}
