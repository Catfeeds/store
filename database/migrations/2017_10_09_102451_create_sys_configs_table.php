<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('need_pay')->default(0);
            $table->unsignedInteger('pic_score')->default(1);
            $table->unsignedInteger('phone_score')->default(1);
            $table->float('pic_price')->default(1);
            $table->float('phone_price')->default(1);
            $table->tinyInteger('show_sign')->default(0);
            $table->tinyInteger('show_qrcode')->default(0);
            $table->tinyInteger('show_share')->default(0);
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
        Schema::dropIfExists('sys_configs');
    }
}
