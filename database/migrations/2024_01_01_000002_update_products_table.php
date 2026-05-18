<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->boolean('has_variations')->default(false)->after('price');
            $table->boolean('track_inventory')->default(true)->after('has_variations');
            $table->unsignedBigInteger('category_id')->nullable()->after('track_inventory');
        });
    }
    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['description','has_variations','track_inventory','category_id']);
        });
    }
};
