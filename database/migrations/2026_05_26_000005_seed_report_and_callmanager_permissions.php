<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $guard = 'web';

        $new = [
            // New report sub-permissions
            'reports.appointments',
            'reports.services',
            'reports.patients',
            'reports.products-sold',
            // Call Manager
            'call-manager.view',
            'call-manager.notes',
        ];

        foreach ($new as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        // Assign all to super-admin
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($new);
        }
    }

    public function down(): void
    {
        $names = [
            'reports.appointments','reports.services',
            'reports.patients','reports.products-sold',
            'call-manager.view','call-manager.notes',
        ];
        Permission::whereIn('name', $names)->delete();
    }
};
