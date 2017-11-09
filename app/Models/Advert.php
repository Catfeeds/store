<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    //
    public function city()
    {
        return $this->hasOne('App\Models\City','id','city_id');
    }
}
