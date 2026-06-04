<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('DEV-XXXXXX shown to user');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token', 80)->unique()->comment('Cookie token stored in browser');
            $table->string('browser')->nullable()->comment('User-Agent string');
            $table->string('ip_address', 45)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('actioned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['token', 'user_id']);
        });

        // Seed the device_approval_enabled setting (OFF by default)
        \App\Models\Setting::firstOrCreate(
            ['key_name' => 'device_approval_enabled'],
            ['key_value' => '0', 'status' => 1]
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('device_approvals');
        \App\Models\Setting::where('key_name', 'device_approval_enabled')->delete();
    }
};
