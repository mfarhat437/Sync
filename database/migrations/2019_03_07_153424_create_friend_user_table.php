<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendUserTable extends Migration
{
    
    
    

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friend_user', function (Blueprint $table) {
//            $table->increments('id')->primary();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('friend_id')->unsigned();
            $table->integer('status')->default(1);
            $table->foreign('friend_id')->references('id')->on('users');
            $table->primary(['user_id', 'friend_id']);
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
        Schema::dropIfExists('friend_user');
    }
}
