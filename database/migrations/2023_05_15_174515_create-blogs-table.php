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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title',255);
            $table->string('short_description',255);
            $table->text('feature_image')->nullable();
            $table->text('long_description')->nullable();
            $table->string('status',10)->default('Draft')->comment('Possible Values Draft, Publish and Inactive');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->index(['user_id','title']);
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
        Schema::dropIfExists('blogs');
    }
};
