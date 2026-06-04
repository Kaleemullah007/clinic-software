<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'pos_sale' to the type enum on inventory_movements
        DB::statement("ALTER TABLE inventory_movements MODIFY COLUMN type ENUM('purchase','appointment_use','return','damaged','adjustment','pos_sale') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inventory_movements MODIFY COLUMN type ENUM('purchase','appointment_use','return','damaged','adjustment') NOT NULL");
    }
};
