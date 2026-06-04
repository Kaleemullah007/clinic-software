<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'pos.view',
            'pos.create',
            'pos.delete',
            'pos.report',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', ['pos.view', 'pos.create', 'pos.delete', 'pos.report'])->delete();
    }
};
