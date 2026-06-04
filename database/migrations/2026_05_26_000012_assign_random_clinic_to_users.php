<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get all clinic IDs
        $clinicIds = DB::table('clinics')->pluck('id')->toArray();

        if (empty($clinicIds)) {
            return; // No clinics yet, nothing to assign
        }

        // Get super-admin user IDs (via Spatie model_has_roles)
        $superAdminRoleId = DB::table('roles')->where('name', 'super-admin')->value('id');

        $superAdminUserIds = $superAdminRoleId
            ? DB::table('model_has_roles')
                ->where('role_id', $superAdminRoleId)
                ->where('model_type', 'App\\Models\\User')
                ->pluck('model_id')
                ->toArray()
            : [];

        // Assign a random clinic_id to every user who has no clinic yet,
        // excluding super-admins
        $users = DB::table('users')
            ->whereNull('clinic_id')
            ->when(!empty($superAdminUserIds), fn($q) => $q->whereNotIn('id', $superAdminUserIds))
            ->pluck('id');

        foreach ($users as $userId) {
            DB::table('users')
                ->where('id', $userId)
                ->update(['clinic_id' => $clinicIds[array_rand($clinicIds)]]);
        }
    }

    public function down(): void
    {
        // Optionally clear all assigned clinic_ids (except super-admin has none anyway)
        DB::table('users')->whereNotNull('clinic_id')->update(['clinic_id' => null]);
    }
};
