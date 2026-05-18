<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessHour>
 */
class BusinessHourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $dt = Carbon::now();
        $from = $dt->addMinutes(10)->format('H:i:s');
        $to = $dt->addHours(9)->format('H:i:s');
        return [
            'is_day'=>1,
            'step'=>30,
            'from'=>$from,
            'to'=>$to
        ];
    }
}
