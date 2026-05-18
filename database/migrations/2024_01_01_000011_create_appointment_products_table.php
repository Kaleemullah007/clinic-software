<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->string('product_name')->comment('Free-text fallback if product_id is null');
            $table->string('product_code')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('doctor_share_amount', 10, 2)->default(0);
            $table->decimal('clinic_share_amount', 10, 2)->default(0);
            $table->boolean('deduct_inventory')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('added_by');
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->foreign('variation_id')->references('id')->on('product_variations')->nullOnDelete();
            $table->foreign('added_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_products');
    }
};
