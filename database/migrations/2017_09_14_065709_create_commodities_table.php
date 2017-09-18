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
            $table->tinyInteger('area_id');
            $table->float('price')->default(0);
            $table->text('description');
            $table->tinyInteger('category_id');
            $table->tinyInteger('state');
            $table->text('detail');
            $table->string('phone');
            $table->string('QQ');
            $table->string('WeChat');
            $table->float('latitude')->default(0)->commit('纬度');
            $table->float('longitude')->default(0)->commit('经度');
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
