<?php

use App\Models\Appointment;
use App\Models\Category;
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
        Schema::create('appointment_services', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Appointment::class)->constrained();
            $table->string('name',50);
            $table->decimal('price',11,2)->default(0);
            $table->decimal('discounted_price',11,2)->default(0);
            $table->decimal('discount',11,2)->default(0);
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')
            ->references('id')->on('categories');
            $table->timestamps();
            $table->index(['service_id','appointment_id','name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_services');
    }
};
