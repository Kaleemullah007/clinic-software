<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    public function up(): void
    {
        Setting::firstOrCreate(
            ['key_name' => 'receipt_style'],
            ['key_value' => 'v1', 'status' => 1]
        );
    }

    public function down(): void
    {
        Setting::where('key_name', 'receipt_style')->delete();
    }
};
