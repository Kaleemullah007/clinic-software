<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

            $this->createAdminRole();
    }

        protected function createRole(RoleName $role, Collection $permissions): void
        {
            $newRole = Role::create(['name' => $role->value]);
            $newRole->permissions()->sync($permissions);
        }


        protected function createAdminRole(): void
        {
            $permissions = Permission::query()
                ->pluck('id');
            $this->createRole(RoleName::ADMIN, $permissions);
        }

        protected function createTesterRole(): void
        {
            $permissions = Permission::query()
                ->pluck('id');
            $this->createRole(RoleName::TESTER, $permissions);
        }

        protected function createFrontDeskRole(): void
        {
            $permissions = Permission::query()
                ->where('name', 'like', 'user.%')
                ->orWhere('name', 'like', 'appointment%')
                ->pluck('id');

            $this->createRole(RoleName::FRONTDESK, $permissions);
        }

        protected function createDocotorRole(): void
        {
            $permissions = Permission::query()
                ->where('name', 'like', 'user.%')
                ->orWhere('name', 'like', 'precription%')
                ->orWhere('name', 'like', 'appointment%')
                ->pluck('id');

            $this->createRole(RoleName::DOCTOR, $permissions);
        }

        protected function createPatientRole(): void
        {
            $permissions = Permission::query()
                ->where('name', 'like', 'user.%')
                ->orWhere('name', 'like', 'precription%')
                ->orWhere('name', 'like', 'appointment%')
                ->pluck('id');

            $this->createRole(RoleName::DOCTOR, $permissions);
        }

        protected function createDeveloperRole(): void
        {
            $permissions = Permission::query()
                ->where('name', 'like', 'user.%')
                ->orWhere('name', 'like', 'precription%')
                ->orWhere('name', 'like', 'appointment%')
                ->pluck('id');

            $this->createRole(RoleName::DEVELOPER, $permissions);
        }

}
