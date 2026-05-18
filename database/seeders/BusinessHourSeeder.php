<?php

namespace Database\Seeders;

use App\Models\BusinessHour;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        $user_ids = User::inRandomOrder()->limit(10)->pluck('id')->toArray();
        // dd($user_ids);
        foreach($days as $day){
        BusinessHour::factory(1)->create([
            'day' => $day,
            'user_id' => 1
        ]);
    }
    }
}
