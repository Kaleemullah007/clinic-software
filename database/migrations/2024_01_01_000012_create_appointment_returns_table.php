<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('appointment_product_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->enum('return_to', ['inventory', 'damaged'])->default('inventory');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('processed_by');
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->foreign('appointment_product_id')->references('id')->on('appointment_products');
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->foreign('variation_id')->references('id')->on('product_variations')->nullOnDelete();
            $table->foreign('processed_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_returns');
    }
};
