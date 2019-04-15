<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longtext('description')->nullable();
            $table->string('place')->nullable();
            $table->integer('min_number')->nullable();
            $table->integer('max_number')->nullable();
            $table->integer('cost')->nullable();
            $table->date('event_date')->nullable();
            $table->time('event_time')->nullable();
            $table->integer('invite_status');
            $table->integer('going_count')->default(0);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('events');
    }
}
