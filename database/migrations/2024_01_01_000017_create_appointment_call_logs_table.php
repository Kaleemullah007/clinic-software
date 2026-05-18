<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_call_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('called_by');
            $table->enum('call_type', ['reminder', 'follow_up', 'reschedule', 'other'])->default('reminder');
            $table->enum('call_status', ['answered', 'no_answer', 'busy', 'scheduled'])->default('answered');
            $table->text('notes')->nullable();
            $table->timestamp('call_at')->nullable()->comment('Scheduled/actual call time');
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->nullOnDelete();
            $table->foreign('patient_id')->references('id')->on('users');
            $table->foreign('called_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_call_logs');
    }
};
