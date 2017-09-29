<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function score()
    {
        return $this->hasOne('App\Models\Score','user_id','id')->pluck('score')->first();
    }
    public function commodities()
    {
        return $this->hasMany('App\Models\Commodity','user_id','id');
    }
}
