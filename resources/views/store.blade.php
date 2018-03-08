<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        * {
            box-sizing: border-box;
        }

        html {
            font-size: 12px;
        }
        /* html,
        body {
          height: 100vh;
        } */

        h1,
        body,
        h3,
        h4,
        p {
            margin: 0;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .container {
            background-color: #eee;
            height: 100vh;
        }

        .title {
            padding: 20px;
            font-size: 1.5rem;
            text-align: center;
            background-color: #6bd897;
            color: #fff;
        }

        .flex-row {
            display: flex;
            flex-direction: row;
        }

        .flex-column {
            display: flex;
            flex-direction: column;
        }
        /* .fix-wrap {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
        } */

        .commodity-item {
            background-color: #fff;
            padding: 10px;
            height: 105px;
        }

        .commodity-item-img {
            width: 25%;
        }

        .commodity-item-infos {
            flex: 1;
            margin: 0 20px;
        }

        .commodity-item-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .commodity-item-time {
            font-size: 1.2rem;
            color: #999;
            margin: 10px 0;
        }

        .commodity-item-sell {
            font-size: 1.2rem;
        }

        .commodity-item-num {
            color: #d5000e;
        }

        .commodity-item-btn {
            width: 50px;
            height: 40px;
            font-size: 1.3rem;
            line-height: 40px;
            text-align: center;
            color: #6bd897;
            border-radius: 5px;
            border: 1px solid #6bd897;
            background-color: #fff;
        }

        .commodity-item-btn:hover {
            background-color: #6bd897;
            color: #fff;
        }
        /* 列表样式 */

        .commodities-list {
            /* padding-top: 190px;
            padding-bottom: 20px; */
            flex: 1;
            overflow-y: scroll;
            margin: 10px 0;
        }

        .commodities-item {
            padding: 10px 20px;
            background-color: #fff;
            border-bottom: 1px solid #eee;
        }

        .commodities-item-id {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .commodities-item-infos {
            align-items: center;
            color: #666;
        }

        .commodities-item-img {
            width: 25%;
        }

        .commodities-item-info {
            flex: 1;
            margin: 0 20px;
        }

        .commodities-item-info-title {
            font-size: 1.25rem;
        }

        .commodities-item-info-address,
        .commodities-item-info-time {
            font-size: 1.1rem;
        }

        .commodities-item-info-address {
            margin: 10px 0;
        }

        .commodities-item-price {
            color: #d5000e;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .download-wrap {
            justify-content: center;
            padding-bottom: 10px;
        }

        .down-btn {
            font-size: 1.2rem;
            /* border: 1px solid #6bd897; */
            border-radius: 5px;
            margin: 0 20px;
            padding: 10px 0;
            width: 150rem;
            text-align: center;
            background-color: #6bd897;
            color: #fff;
            text-decoration: none;
        }

        .down-btn:active {
            background-color: #5fc187;
        }
    </style>
    <title>扫描结果</title>
</head>

<body>
<section class="container flex-column">
    <div class="fix-wrap">
        {{--<h1 class="title">{{$user->name}}的消息</h1>--}}

        <!-- 第一个商品信息 -->
        <div class="commodity-item flex-row">
            <img class="commodity-item-img" src="{{$user->avatar}}" alt="商品图片">
            <div class="commodity-item-infos">
                <h3 class="commodity-item-title">{{$user->name}}</h3>
                <p class="commodity-item-time">注册时间 {{$user->created_at}}</p>
                <p class="commodity-item-sell">已上架
                    <span class="commodity-item-num">{{$count}}</span>
                </p>
            </div>
            <!-- <div class="commodity-item-btn">关注</div> -->
        </div>
        <!-- /第一个商品信息 -->
    </div>
    <!-- 商品列表 -->
    <ul class="commodities-list">
        <!-- 循环的商品项 -->
    @foreach($commodities as $commodity)
            <li class="commodities-item">
                <h3 class="commodities-item-id">编号 {{$commodity->id}}</h3>
                <div class="commodities-item-infos flex-row">
                    <img class="commodities-item-img" src="{{$commodity->pictures()->pluck('thumb_url')->first()}}" alt="商品图片">
                    <div class="commodities-item-info">
                        <h4 class="commodities-item-info-title">{{$commodity->title}}</h4>
                        <p class="commodities-item-info-address">{{$commodity->address}}</p>
                        <p class="commodities-item-info-time">{{$commodity->created_at}}</p>
                    </div>
                    <div class="commodities-item-price">￥{{$commodity->price}}</div>
                </div>
            </li>

        @endforeach

        <!-- /循环的商品项 -->
    </ul>

    <div class="download-wrap flex-row">
        <a class="down-btn" href="{{$config->ios_url}}">
            IOS 下载
        </a>
        <a class="down-btn" href="{{$config->android_url}}">
            Android 下载
        </a>
    </div>
    <!-- /商品列表 -->
</section>
</body>

</html>