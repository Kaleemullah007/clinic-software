<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('benchmark_price', 10, 2)->nullable()->default(null)->after('price')
                  ->comment('Minimum acceptable price; warn doctor if service is priced below this');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('benchmark_price');
        });
    }
};
