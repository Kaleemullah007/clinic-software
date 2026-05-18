<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('salary_type', ['fixed', 'hourly', 'commission'])->default('fixed')->after('role');
            $table->decimal('salary_amount', 12, 2)->default(0)->after('salary_type');
            $table->string('bank_account')->nullable()->after('salary_amount');
            $table->string('cnic')->nullable()->after('bank_account');
            $table->date('joining_date')->nullable()->after('cnic');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salary_type', 'salary_amount', 'bank_account', 'cnic', 'joining_date']);
        });
    }
};
