<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bb_comment_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('rating');
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
        Schema::dropIfExists('bb_comment_ratings');
    }
}
