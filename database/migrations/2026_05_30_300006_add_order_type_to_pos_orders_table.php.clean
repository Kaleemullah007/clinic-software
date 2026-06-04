<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->enum('order_type', ['takeaway', 'delivery'])->default('takeaway')->after('payment_status');
            $table->text('delivery_address')->nullable()->after('order_type');
        });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'delivery_address']);
        });
    }
};
