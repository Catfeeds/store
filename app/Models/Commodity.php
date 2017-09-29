<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commodity extends Model
{
    //
    public function pictures()
    {
        return $this->hasMany('App\Models\CommodityPicture','commodity_id','id');
    }
}
