<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [

            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'date'=>fake()->date('Y-m-d'),
            'time'=>fake()->time(),
            'user_id'=>User::inRandomOrder()->limit(1)->get()->value('id'),
            'service_id'=>Category::inRandomOrder()->limit(1)->get()->value('id'),
            'is_paid' => 'pending',
            'appointment_status' => 'pending'

        ];
    }
}
