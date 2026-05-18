<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->createAdminUser();
    }

    public function createAdminUser()
    {
        User::create([
            'name'     => 'Rao',
            'email'    => 'admin@admin.com',
            'phone' => fake()->phoneNumber(),
            'password' => bcrypt('password'),
            'role'=>'admin',
        ])->roles()->sync(Role::where('name', RoleName::ADMIN->value)->first());


        User::create([
            'name'     => 'Dr Mubashir Daha',
            'email'    => 'dr_daha885@hotmail.com',
            'phone' => fake()->phoneNumber(),
            'password' => bcrypt('password'),
            'role'=>'admin',
        ])->roles()->sync(Role::where('name', RoleName::ADMIN->value)->first());



    }

}
