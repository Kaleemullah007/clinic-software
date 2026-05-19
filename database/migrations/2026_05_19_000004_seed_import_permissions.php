<?php
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $perms = ['imports.view', 'imports.create', 'imports.rollback'];
        foreach ($perms as $name) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        $role = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
        if ($role) $role->givePermissionTo($perms);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
    public function down(): void {
        \Spatie\Permission\Models\Permission::whereIn('name', ['imports.view','imports.create','imports.rollback'])->delete();
    }
};
