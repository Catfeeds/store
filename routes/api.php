<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix'=>'v1','middleware'=>'cross'],function (){
    Route::post('register','API\V1\UserController@register');
    Route::post('login','API\V1\UserController@login');
    Route::get('token','API\V1\UserController@getToken');
    Route::get('commodities','API\V1\CommodityController@getCommodities');
    Route::get('types','API\V1\CommodityController@getCommodityTypes');
    Route::post('commodity','API\V1\CommodityController@addCommodity');
});
