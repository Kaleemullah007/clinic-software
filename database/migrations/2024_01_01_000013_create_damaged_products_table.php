<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('cost_value', 10, 2)->default(0);
            $table->text('reason')->nullable();
            $table->string('reference_type')->nullable()->comment('appointment_return or manual');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('reported_by');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('variation_id')->references('id')->on('product_variations')->nullOnDelete();
            $table->foreign('reported_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_products');
    }
};
