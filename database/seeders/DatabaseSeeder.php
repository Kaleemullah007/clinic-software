<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run permissions only (no demo data)
        $this->call([
            PermissionSeeder::class,
        ]);
    }

    /**
     * Run with full demo data:
     *   php artisan db:seed --class=DemoSeeder
     */
}
