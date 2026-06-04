<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->enum('type', ['prescription', 'note'])->default('prescription')->after('id');
        });

        // Mark all existing rows as prescriptions
        DB::table('prescriptions')->update(['type' => 'prescription']);
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
