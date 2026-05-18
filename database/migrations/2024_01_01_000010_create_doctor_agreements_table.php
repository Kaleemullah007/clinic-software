<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->enum('share_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('doctor_share', 8, 2)->comment('% or fixed amount for doctor');
            $table->decimal('clinic_share', 8, 2)->comment('% or fixed amount for clinic');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('doctor_id')->references('id')->on('users');
            $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_agreements');
    }
};
