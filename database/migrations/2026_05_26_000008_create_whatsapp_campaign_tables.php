<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Templates ────────────────────────────────────────────────────────
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('message_type', ['text', 'image', 'both'])->default('text');
            $table->text('message_body')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });

        // ── Campaigns ────────────────────────────────────────────────────────
        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('template_id');
            $table->foreign('template_id')->references('id')->on('whatsapp_templates')->cascadeOnDelete();
            $table->string('target_role');                          // e.g. patient, doctor
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->foreign('clinic_id')->references('id')->on('clinics')->nullOnDelete();
            $table->foreign('doctor_id')->references('id')->on('users')->nullOnDelete();
            $table->datetime('scheduled_at');
            $table->string('timezone')->default('Asia/Karachi');
            $table->unsignedInteger('message_delay')->default(2);   // seconds between messages
            $table->enum('status', ['draft','scheduled','running','completed','failed'])->default('scheduled');
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('pending_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });

        // ── Campaign Logs ────────────────────────────────────────────────────
        Schema::create('whatsapp_campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('whatsapp_campaigns')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('phone');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->string('meta_message_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_campaign_logs');
        Schema::dropIfExists('whatsapp_campaigns');
        Schema::dropIfExists('whatsapp_templates');
    }
};
