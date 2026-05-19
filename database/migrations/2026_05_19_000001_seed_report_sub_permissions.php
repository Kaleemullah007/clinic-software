<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $new = [
            'reports.revenue',
            'reports.expenses',
            'reports.inventory',
            'reports.salaries',
            'reports.doctor-performance',
        ];

        foreach ($new as $name) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => 'web',
            ]);
        }

        // Give them all to super-admin automatically
        $superAdmin = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($new);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        \Spatie\Permission\Models\Permission::whereIn('name', [
            'reports.revenue',
            'reports.expenses',
            'reports.inventory',
            'reports.salaries',
            'reports.doctor-performance',
        ])->delete();
    }
};
