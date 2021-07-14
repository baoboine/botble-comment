<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserTypeToCommentUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comment_users', function (Blueprint $table) {
            $table->string('user_type', 255)->default(addslashes(\Botble\Comment\Models\CommentUser::class));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comment_users', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
}
