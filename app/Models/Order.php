<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    public static $order_type_income = 1;
    public static $order_type_outcome = 2;
    public static $order_state_create = 0;
    public static $order_state_handle = 1;
    public static $order_state_finish = 2;
    public static $order_state_fail = 3;
}
