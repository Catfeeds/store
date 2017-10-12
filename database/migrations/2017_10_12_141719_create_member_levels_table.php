<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('level')->unique();
            $table->float('price')->default(0);
            $table->integer('time')->default(0);
            $table->unsignedInteger('send_daily')->default(0);
            $table->unsignedInteger('send_max')->default(0);
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
        Schema::dropIfExists('member_levels');
    }
}
