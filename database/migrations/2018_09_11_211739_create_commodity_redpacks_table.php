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
            $table->integer('cash_number')->default(0);
            $table->tinyInteger('distance');
            $table->float('cash_all');
            $table->float('cash_min');
            $table->float('cash_max');
            $table->string('title');
            $table->float('coupon_all')->default(0);
            $table->float('coupon_min')->default(0);
            $table->float('coupon_max')->default(0);
            $table->integer('coupon_number')->default(0);
            //$table->integer('coupon_start');
            $table->integer('coupon_end')->default(0);
            $table->string('code',4)->nullable();
            $table->string('coupon_title')->nullable();
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
