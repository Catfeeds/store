<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedpacketConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redpacket_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('state')->default(0);
            $table->tinyInteger('cash_ratio');
            $table->tinyInteger('cash_max_day');
            $table->tinyInteger('cash_min_day');
            $table->float('cash_price_max');
            $table->float('cash_price_min');
            $table->float('cash_total_max');
            $table->float('cash_total_min');
            $table->integer('cash_number_max');
            $table->integer('cash_number_min');
            $table->tinyInteger('coupon_ratio');
            $table->tinyInteger('coupon_max_day');
            $table->tinyInteger('coupon_min_day');
            $table->float('coupon_price_max');
            $table->float('coupon_price_min');
            $table->float('coupon_total_max');
            $table->float('coupon_total_min');
            $table->integer('coupon_number_max');
            $table->integer('coupon_number_min');
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
        Schema::dropIfExists('redpacket_configs');
    }
}
