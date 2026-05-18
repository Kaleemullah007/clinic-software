<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key_name' => 'reminder_days',   'key_value' => '1'],
            ['key_name' => 'pricing_mode',    'key_value' => 'fixed'],
            ['key_name' => 'whatsapp_prefix', 'key_value' => '+92'],
            ['key_name' => 'currency_symbol', 'key_value' => 'PKR'],
            ['key_name' => 'receipt_message', 'key_value' => 'Thank you for visiting us! Your receipt is attached.'],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->updateOrInsert(
                ['key_name' => $row['key_name']],
                ['key_value' => $row['key_value'], 'status' => 1]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key_name', [
            'reminder_days', 'pricing_mode', 'whatsapp_prefix', 'currency_symbol', 'receipt_message',
        ])->delete();
    }
};
