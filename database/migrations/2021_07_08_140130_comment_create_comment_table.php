<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CommentCreateCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bb_comments', function (Blueprint $table) {
            $table->id();
            $table->longText('comment');
            $table->integer('reference_id')->unsigned();
            $table->string('reference_type', 120);
            $table->string('ip_address', 39)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('status', 60)->default('published');
            $table->integer('like_count')->default(0);
            $table->timestamps();
        });

        Schema::create('bb_comment_users', function(Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('bb_comment_likes', function(Blueprint $table) {
            $table->id();
            $table->integer('comment_id')->unsigned()->references('id')->on('comments')->index();
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
        Schema::dropIfExists('bb_comments');
        Schema::dropIfExists('bb_comment_users');
    }
}
