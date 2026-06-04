<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();       // patient
            $table->decimal('subtotal',    10, 2)->default(0);
            $table->decimal('discount',    10, 2)->default(0);
            $table->string('tax_label', 50)->nullable();                                 // e.g. "GST"
            $table->decimal('tax_rate',   5, 2)->default(0);                             // percentage
            $table->decimal('tax_amount', 10, 2)->default(0);                            // computed
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->text('shipping_address')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_orders');
    }
};
