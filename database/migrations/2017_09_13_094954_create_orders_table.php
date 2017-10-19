<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('number');
            $table->float('price');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('content');
            $table->tinyInteger('type');
            //1->充值,2->支付,3->退款
            $table->tinyInteger('state')->default(0);
            $table->tinyInteger('pay_type');
            //1->积分,2->支付宝,3->微信
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
        Schema::dropIfExists('orders');
    }
}
