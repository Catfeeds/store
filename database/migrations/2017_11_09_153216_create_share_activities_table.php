<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('start');
            $table->integer('end');
            $table->unsignedTinyInteger('score');
            $table->string('rule',3000);
            $table->string('content',3000);
            $table->tinyInteger('state')->default(0);
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
        Schema::dropIfExists('share_activities');
    }
}