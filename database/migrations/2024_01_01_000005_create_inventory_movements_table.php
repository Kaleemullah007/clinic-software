<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->enum('type', ['purchase','appointment_use','return','damaged','adjustment']);
            $table->integer('quantity'); // positive = in, negative = out
            $table->decimal('unit_price', 11, 2)->default(0);
            $table->string('reference_type')->nullable(); // App\Models\Purchase etc
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('appointment_code')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_movements'); }
};
