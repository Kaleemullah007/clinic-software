<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── whatsapp_logs ────────────────────────────────────────────────────
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sent_by')->constrained('users')->cascadeOnDelete();
            $table->string('phone', 20)->nullable();
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->string('meta_message_id')->nullable()->comment('Message ID returned by Meta API');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['appointment_id']);
            $table->index(['sent_by']);
            $table->index(['status']);
            $table->index(['created_at']);
        });

        // ── appointments: add whatsapp_sent_at ───────────────────────────────
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('whatsapp_sent_at')->nullable()->after('updated_at')
                  ->comment('First time a WhatsApp receipt was sent for this appointment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('whatsapp_sent_at');
        });
    }
};
