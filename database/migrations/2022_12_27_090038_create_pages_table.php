<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('meta_tag',255)->nullable();
            $table->string('meta_description',170)->nullable();
            $table->string('keywords',65)->nullable();
            $table->string('heading',255);
            $table->text('description')->nullable();
            $table->string('procedure_heading',255);
            $table->text('procedure_description')->nullable();
            $table->string('title',65);
            $table->unsignedBigInteger('category_id');
            $table->boolean('is_discounted')->default(false);
            $table->decimal('price',11,2)->default(0);
            $table->decimal('discounted_price',11,2)->default(0);
            $table->boolean('is_button_availalble')->default(false);
            $table->string('url',255);
            $table->string('slug',255);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
