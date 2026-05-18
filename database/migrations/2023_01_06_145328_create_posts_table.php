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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->text('content');
            // $table->unsignedBigInteger('user_id');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->index(['user_id']);
            $table->date('publish_date');
            $table->integer('status')->default(1)->comment('1 for Draft 2 for publish 3 for inactive');
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
        Schema::dropIfExists('posts');
    }
};
