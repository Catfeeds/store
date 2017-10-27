<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('user_id');
            $table->float('price')->default(0);
            $table->text('description');
            $table->tinyInteger('state')->defalut(0);
            $table->text('detail');
            $table->string('phone');
            $table->string('address');
            $table->string('QQ')->nullable();
            $table->string('WeChat')->nullable();
            $table->float('latitude')->default(0)->commit('纬度');
            $table->float('longitude')->default(0)->commit('经度');
            $table->tinyInteger('enable')->default(0);
            $table->tinyInteger('pass')->default(0);
            $table->unsignedInteger('read')->default(0);
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
        Schema::dropIfExists('commodities');
    }
}
