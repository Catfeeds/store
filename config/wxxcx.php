<?php

return [
    'wechat' => [
        'appid' => 'wx4e3c91da73451779',              // APPID
        'mch_id' => '1489748612',             // 微信商户号
        'notify_url' => 'http://app.ankekan.com/api/v1/pay/notify',
        'key' => 'qwertyuiop1234567890qwertyuiop12',                // 微信支付签名秘钥
        'secret' => '61138b8e364320349347da0ff4d95f47',                // 微信支付签名秘钥
        'cert_client' => './apiclient_cert.pem',        // 客户端证书路径，退款时需要用到
        'cert_key' => './apiclient_key.pem',            // 客户端秘钥路径，退款时需要用到
    ],
];

