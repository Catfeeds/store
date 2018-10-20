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
    Route::post('reset/password','API\V1\UserController@resetPassword');
    Route::get('token','API\V1\UserController@getToken');
    Route::post('modify/phone','API\V1\UserController@modifyPhone');
    Route::get('modify/phone/verify','API\V1\SmsController@sendModifySMS');
    Route::get('commodities','API\V1\CommodityController@getCommodities');
    Route::get('types','API\V1\CommodityController@getCommodityTypes');
    Route::post('commodity','API\V1\CommodityController@addCommodity')->middleware('limitSend');
    Route::get('commodity/{id}','API\V1\CommodityController@getCommodity');
    Route::delete('commodity/{id}','API\V1\CommodityController@deleteCommodity');
    Route::any('upload','API\V1\UploadController@uploadImage');
    Route::get('sign','API\V1\UserController@sign');
    Route::get('signs','API\V1\UserController@signRecord');
    Route::get('user','API\V1\UserController@UserInfo');
    Route::get('my/commodities','API\V1\UserController@getMyCommodities');
    Route::post('report','API\V1\CommodityController@addReport');
    Route::post('reject','API\V1\CommodityController@addReportReject');
    Route::get('levels','API\V1\UserController@getLevels');
    Route::get('sms','API\V1\SmsController@send');
    Route::post('member','API\V1\OrderController@addMember');
    Route::get('launch/image','API\V1\LaunchImageController@getLaunchImage');
    Route::post('buy/picture','API\V1\OrderController@buyCommodityPicture');
    Route::post('buy/contact','API\V1\OrderController@buyCommodityPhone');
    Route::post('picture/{id}','API\V1\CommodityController@addPicture');
    Route::get('del/picture/{id}','API\V1\CommodityController@delPicture');
    Route::post('user','API\V1\UserController@setUserInfo');
    Route::get('cities','API\V1\SystemController@getAllCities');
    Route::get('store/{id}','API\V1\CommodityController@getStore');
    Route::get('basic/store/{id}','API\V1\CommodityController@getBasicStore');
    Route::post('collect','API\V1\CommodityController@addCollect');
    Route::post('del/collect','API\V1\CommodityController@delCollect');
    Route::get('collects','API\V1\CommodityController@getCollects');
    Route::post('attention','API\V1\CommodityController@addAttention');
    Route::post('del/attention','API\V1\CommodityController@delAttention');
    Route::get('attentions','API\V1\CommodityController@getAttentions');
    Route::get('city','API\V1\SystemController@setCity');
    Route::get('messages','API\V1\SystemController@getMessages');
    Route::get('adverts','API\V1\SystemController@getAdverts');
    Route::get('message/{id}','API\V1\SystemController@getMessage');
    Route::get('read/message/{id}','API\V1\SystemController@readMessage');
    Route::get('del/message/{id}','API\V1\SystemController@delMessage');
    Route::get('orders','API\V1\OrderController@getOrders');
    Route::post('parttime','API\V1\CommodityController@addPartTime');
    Route::get('descriptions','API\V1\CommodityController@getDescriptions');
    Route::get('around','API\V1\CommodityController@getRound');
    Route::get('city/count','API\V1\CommodityController@countCity');
    Route::get('find/user','API\V1\UserController@findUser');
    Route::get('qrcode','API\V1\SystemController@getQrCode');
    Route::get('reasons','API\V1\SystemController@getReportReasons');
    Route::get('modify/commodity/{id}','API\V1\CommodityController@modifyCommodity');
    Route::get('guides','API\V1\SystemController@getUserGuides');
    Route::get('config','API\V1\SystemController@getSystemConfig');
    Route::post('oauth/login','API\V1\UserController@OauthLogin');
    Route::get('oauth/search','API\V1\UserController@OauthSearch');
    Route::post('bind/qq','API\V1\UserController@bindQQ');
    Route::post('bind/wechat','API\V1\UserController@bindWeChat');
    Route::get('scan','API\V1\UserController@scan');
    Route::post('pay/notify','API\V1\OrderController@pay_notify');
    Route::any('alipay/notify','API\V1\OrderController@alipayNotify');
    Route::get('share/activity','API\V1\SystemController@getShareActivity');
    Route::get('article','API\V1\SystemController@getArticle');
    Route::post('unbind','API\V1\UserController@unbind');
    Route::post('commodity/redpacket','V2\RedPacketController@addCommodityRedPacket');
    Route::get('commodity/redpacket','V2\RedPacketController@getCommodityRedPacket');
    Route::get('user/amount','V1\UserController@getUserAmount');
    Route::post('user/amount','V1\OrderController@addUserAmount');
});
