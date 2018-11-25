<?php

return [
    'wechat' => [
        'appid' => 'wx6555199310111c30',              // APPID
        'app_id' => 'wx6555199310111c30',              // APPID
        'mch_id' => '1511912871',             // 微信商户号
        'notify_url' => 'http://app.ankekan.com/api/v1/pay/notify',
        'key' => 'DFffffffffffddddf54354364365464D',                // 微信支付签名秘钥
        'secret' => '74cb80869a0901b312f858617ec792f2',                // 微信支付签名秘钥
        'cert_client' => 'cert/apiclient_cert.pem',        // 客户端证书路径，退款时需要用到
        'cert_key' => 'cert/apiclient_key.pem',            // 客户端秘钥路径，退款时需要用到
    ],
];

