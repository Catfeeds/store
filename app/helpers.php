<?php

if (!function_exists('createNonceStr')){
    function createNonceStr($length = 15) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str.time();
    }
}
if (!function_exists('setUserToken')){
    function setUserToken($key,$value)
    {
        \Illuminate\Support\Facades\Redis::set($key,$value);
    }
}
if (!function_exists('getUserToken')) {
    function getUserToken($key)
    {
        $uid = \Illuminate\Support\Facades\Redis::get($key);
        if (!isset($uid)){
            return false;
        }
        return $uid;
    }
}

if (!function_exists('getCountSql')) {
    function getCountSql($user_id,$start,$end)
    {
        return "SELECT DATE_FORMAT(`created_at`,'%d') as date FROM signs WHERE `user_id` ="." $user_id"." AND `created_at` BETWEEN '".$start."' AND '".$end."'";
    }
}
if (!function_exists('setCode')){
    function setCode($key,$value)
    {
        \Illuminate\Support\Facades\Redis::set($key,$value);
        \Illuminate\Support\Facades\Redis::expire($key,300);

    }
}
if (!function_exists('getCode')) {
    function getCode($phone)
    {
        $code = \Illuminate\Support\Facades\Redis::get($phone);
        if (!isset($code)){
            return false;
        }
        return unserialize($code);
    }
}
if (!function_exists('Push')) {
    function Push()
    {
        \Zzl\Umeng\Facades\Umeng::ios()->push();
    }
}
if (!function_exists('formatUrl')) {
    function formatUrl($url)
    {
        return env('APP_URL').$url;
    }
}

if (!function_exists('getAround')){
     function getAround($lat,$lon,$raidus){
        $PI = 3.14159265;

        $latitude = $lat;
        $longitude = $lon;

        $degree = (24901*1609)/360.0;
        $raidusMile = $raidus;

        $dpmLat = 1/$degree;
        $radiusLat = $dpmLat*$raidusMile;
        $minLat = $latitude - $radiusLat;
        $maxLat = $latitude + $radiusLat;

        $mpdLng = $degree*cos($latitude * ($PI/180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = $dpmLng*$raidusMile;
        $minLng = $longitude - $radiusLng;
        $maxLng = $longitude + $radiusLng;
        return [
            'minLat'=>round($minLat,7),
            'maxLat'=>round($maxLat,7),
            'minLng'=>round($minLng,7),
            'maxLng'=>round($maxLng,7),
        ];
    }
}