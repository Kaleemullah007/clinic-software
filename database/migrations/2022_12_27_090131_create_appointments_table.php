<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('email',255)->nullable();
            $table->string('phone',255);
            $table->string('whatsapp_number',255);
            $table->date('date')->nullable();
            $table->dateTime('time')->nullable();
            $table->boolean('gender')->default(0)->comment('o for f 1 for male');
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('price',11,2);   //  total modification in price
            $table->decimal('discounted_price',11,2);
            $table->decimal('subtotal_price',11,2);   //  total modification in price
            $table->decimal('subtotal_discounted_price',11,2);
            $table->decimal('subtotal_price_after_discount',11,2);
            $table->decimal('subtotal_discounted_price_after_discount',11,2);
            $table->decimal('remaining_amount',11,2);
            $table->decimal('paid_amount',11,2);


            $table->decimal('discount',11,2);
            $table->string('coupon',30)->nullable();
            $table->string('is_paid',30)->default('pending');
            $table->string('appointment_status',30)->default('pending');
            $table->timestamps();
            $table->index(['doctor_id','clinic_id','name','phone']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
