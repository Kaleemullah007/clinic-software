<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perm = Permission::firstOrCreate(
            ['name' => 'reports.service-gap', 'guard_name' => 'web']
        );

        // Grant to Super Admin automatically
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin && ! $superAdmin->hasPermissionTo($perm)) {
            $superAdmin->givePermissionTo($perm);
        }
    }

    public function down(): void
    {
        Permission::where('name', 'reports.service-gap')->delete();
    }
};
