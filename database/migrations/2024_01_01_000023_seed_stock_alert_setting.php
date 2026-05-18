<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Insert default stock_alert setting if it doesn't already exist
        $exists = DB::table('settings')->where('key_name', 'stock_alert')->exists();
        if (!$exists) {
            DB::table('settings')->insert([
                'key_name'   => 'stock_alert',
                'key_value'  => '5',
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('settings')->where('key_name', 'stock_alert')->delete();
    }
};
