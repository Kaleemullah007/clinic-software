<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    public function up(): void
    {
        Setting::firstOrCreate(
            ['key_name' => 'default_receipt'],
            ['key_value' => 'services_receipt', 'status' => 1]
        );
    }

    public function down(): void
    {
        Setting::where('key_name', 'default_receipt')->delete();
    }
};
