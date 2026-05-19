<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            // Replace the generic filters column with a structured search_criteria column
            if (Schema::hasColumn('import_logs', 'filters')) {
                $table->dropColumn('filters');
            }
            $table->json('search_criteria')->nullable()->after('column_mapping');
        });
    }

    public function down(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            if (Schema::hasColumn('import_logs', 'search_criteria')) {
                $table->dropColumn('search_criteria');
            }
            $table->json('filters')->nullable()->after('column_mapping');
        });
    }
};
