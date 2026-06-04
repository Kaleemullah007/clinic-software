<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $guard = 'web';
        $perms = [
            'whatsapp-campaign.view',
            'whatsapp-campaign.create',
            'whatsapp-campaign.send',
            'whatsapp-campaign.templates',
        ];

        foreach ($perms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($perms);
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', [
            'whatsapp-campaign.view','whatsapp-campaign.create',
            'whatsapp-campaign.send','whatsapp-campaign.templates',
        ])->delete();
    }
};
