<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommodityRedpacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_redpacks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('commodity_id');
            $table->string('icon');
            $table->integer('start');
            $table->integer('end');
            $table->integer('number')->default(0);
            $table->tinyInteger('distance');
            $table->float('cash_all');
            $table->float('cash_min');
            $table->float('cash_max');
            $table->string('title');
            $table->float('coupon_all');
            $table->float('coupon_min');
            $table->float('coupon_max');
            //$table->integer('coupon_start');
            $table->integer('coupon_end');
            $table->string('code',4);
            $table->string('coupon_title');
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
        Schema::dropIfExists('commodity_redpacks');
    }
}
