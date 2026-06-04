<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Permissions grouped by module — used by seeder AND permission UI controllers/views.
     */
    public static array $modules = [
        // Core
        'dashboard'          => ['view'],
        'appointments'       => ['view', 'create', 'edit', 'delete'],
        'users'              => ['view', 'create', 'edit', 'delete'],
        'roles'              => ['view', 'create', 'edit', 'delete'],
        'permissions'        => ['view', 'create', 'edit', 'delete'],
        'categories'         => ['view', 'create', 'edit', 'delete'],
        'clinics'            => ['view', 'create', 'edit', 'delete'],
        'business-hours'     => ['view', 'create', 'edit', 'delete'],
        'prescriptions'      => ['view', 'create', 'edit', 'delete'],
        'blogs'              => ['view', 'create', 'edit', 'delete'],
        'pages'              => ['view', 'create', 'edit', 'delete'],
        'media'              => ['view', 'create', 'edit', 'delete'],
        'email-templates'    => ['view', 'create', 'edit', 'delete'],
        'placeholders'       => ['view', 'create', 'edit', 'delete'],
        'contacts'           => ['view', 'delete'],
        'settings'           => ['view', 'edit'],
        // New modules
        'vendors'            => ['view', 'create', 'edit', 'delete'],
        'products'           => ['view', 'create', 'edit', 'delete'],
        'inventory'          => ['view', 'create', 'edit', 'delete'],
        'purchase-requests'  => ['view', 'create', 'edit', 'delete', 'approve'],
        'purchases'          => ['view', 'create', 'edit', 'delete'],
        'doctor-agreements'  => ['view', 'create', 'edit', 'delete'],
        'appointment-products' => ['view', 'create', 'edit', 'delete'],
        'returns'            => ['view', 'create', 'delete'],
        'damaged-products'   => ['view', 'create', 'delete'],
        'expenses'           => ['view', 'create', 'edit', 'delete'],
        'salaries'           => ['view', 'create', 'edit', 'delete'],
        'call-logs'          => ['view', 'create', 'edit', 'delete'],
        'consent-forms'      => ['view', 'create', 'edit', 'delete'],
        'before-after-photos'=> ['view', 'create', 'delete'],
        'reports'            => ['view', 'revenue', 'expenses', 'inventory', 'salaries', 'doctor-performance', 'service-gap', 'product-gap'],
        'taxonomy'           => ['manage'],
        'imports'            => ['view', 'create', 'rollback'],
        'device-approvals'   => ['manage'],
    ];

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        $allPermissions = [];
        foreach (self::$modules as $module => $actions) {
            foreach ($actions as $action) {
                $perm = Permission::firstOrCreate(
                    ['name' => "{$module}.{$action}", 'guard_name' => 'web']
                );
                $allPermissions[] = $perm;
            }
        }

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($allPermissions);

        // Doctor role — clinical access
        $doctor = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        $doctor->syncPermissions(
            Permission::whereIn('name', [
                'dashboard.view',
                'appointments.view', 'appointments.create', 'appointments.edit',
                'prescriptions.view', 'prescriptions.create', 'prescriptions.edit',
                'appointment-products.view', 'appointment-products.create', 'appointment-products.edit',
                'consent-forms.view', 'consent-forms.create',
                'before-after-photos.view', 'before-after-photos.create',
                'call-logs.view', 'call-logs.create',
            ])->get()
        );

        // Receptionist role
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $receptionist->syncPermissions(
            Permission::whereIn('name', [
                'dashboard.view',
                'appointments.view', 'appointments.create', 'appointments.edit',
                'patients.view',
                'call-logs.view', 'call-logs.create', 'call-logs.edit',
                'contacts.view',
            ])->get()
        );

        // Pharmacist / Store role
        $pharmacist = Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
        $pharmacist->syncPermissions(
            Permission::whereIn('name', [
                'dashboard.view',
                'inventory.view', 'inventory.create', 'inventory.edit',
                'products.view',
                'vendors.view',
                'purchase-requests.view', 'purchase-requests.create',
                'purchases.view', 'purchases.create',
                'returns.view', 'returns.create',
                'damaged-products.view', 'damaged-products.create',
            ])->get()
        );

        // Patient role — read-only
        $patient = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        $patient->syncPermissions(
            Permission::whereIn('name', ['dashboard.view', 'appointments.view'])->get()
        );

        // Assign super-admin to first user
        $adminUser = User::find(1);
        if ($adminUser) {
            $adminUser->syncRoles(['super-admin']);
        }

        $this->command->info('✅  Permissions & roles seeded. User #1 assigned super-admin.');
    }
}
