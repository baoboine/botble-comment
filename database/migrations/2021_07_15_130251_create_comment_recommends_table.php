<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentRecommendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bb_comment_recommends', function (Blueprint $table) {
            $table->id();
            $table->integer('reference_id')->unsigned();
            $table->string('reference_type', 120);
            $table->integer('user_id')->unsigned()->references('id')->on('comment_users')->index();
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
        Schema::dropIfExists('bb_comment_recommends');
    }
}
