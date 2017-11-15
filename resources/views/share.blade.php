<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>分享</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html {
            font-size: 12px;
        }

        h1,
        body,
        h3,
        h4,
        p {
            margin: 0;
        }

        .container {
            height: 100vh;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }

        .flex-column {
            display: flex;
            flex-direction: column;
        }

        .flex-row {
            display: flex;
            flex-direction: row;
        }

        .code-number-wrap {
            background-color: #6bd897;
            width: 20rem;
            color: #fff;
        }

        .code-number-wrap span {
            padding: 0 30px;
            height: 60px;
            line-height: 60px;
        }

        .code-number-wrap span:first-child {
            border-right: 5px dotted #fff;
            font-size: 1.1rem;
        }

        .code-number-wrap span:last-child {
            font-size: 1.5rem;
        }

        .code-image {
            width: 70%;
        }

        .code-btn {
            width: 96%;
            border-radius: 5px;
            background-color: #6bd897;
            color: #fff;
            font-size: 1.6rem;
            padding: 15px 0;
            text-align: center;
            text-decoration: none;
        }

        .code-btn:active {
            background-color: #61c589;
        }

        .code-title {
            font-size: 1.1rem;
        }

        .code-content {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 10px;
        }
    </style>
</head>

<body>
<section class="container flex-column">
    <div>
        <h3 class="code-title">Hi 我是 xxx，邀请你来加入 xxx。</h3>
        <p class="code-content">使用我的邀请码注册，即可xxx</p>
    </div>
    <div class="code-number-wrap flex-row">
        <span>邀请码</span>
        <span>{{$code}}</span>
    </div>
    <img class="code-image" src="http://seopic.699pic.com/photo/00026/7248.jpg_wh1200.jpg" alt="图片">
    <a class="code-btn" href="#">下载xxx,发现xxx</a>
</section>
</body>

</html>