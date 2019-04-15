<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status')->default(0);
            $table->integer('sender_id')->unsigned();
            $table->foreign('sender_id')->references('id')->on('users');
            $table->integer('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('events');
            $table->integer('recipient_id')->unsigned();
            $table->foreign('recipient_id')->references('id')->on('users');
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
        Schema::dropIfExists('invitations');
    }
}
