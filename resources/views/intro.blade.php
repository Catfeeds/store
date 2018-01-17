<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>安珂看看</title>
</head>
<style>
    * {
        box-sizing: border-box;
    }

    html {
        font-size: 12px;
    }

    body,
    html {
        margin: 0;
    }

    .pattern {
        width: 18%;
        position: absolute;
        top: 0;
        z-index: 1;
        max-width: 312px;
        transition: all .5s;
    }

    .pattern.left {
        left: 0;
    }

    .pattern.right {
        right: 0;
    }

    .pattern img {
        width: 100%;
        pointer-events: none;
        transition: all 1s;
    }

    #app {
        display: table;
        width: 100%;
        height: 100vh;
        background-color: #fff;
    }

    .content {
        display: table-cell;
        height: 100%;
        width: 100%;
        vertical-align: middle;
        text-align: center;
    }

    .logo {
        width: 50%;
    }

    h1 {
        color: #505556;
        margin-top: 0;
        font-size: 24px;
        font-weight: 400;
        position: relative;
    }

    h1::after {
        content: '';
        height: 1px;
        position: absolute;
        background-color: #DAE2E3;
        width: 60%;
        left: 0;
        right: 0;
        margin: auto;
        bottom: -20px;
    }

    .btns {
        margin-top: 5rem;
        overflow: hidden;
    }

    .btns a {
        display: block;
        margin: 15px auto;
        transition: all .25s;
        padding: 12px 46px;
        min-width: 200px;
        max-width: 250px;
        border: 1px solid #32B2A7;
        border-radius: 40px;
        font-size: 14px;
        background-color: #32B2A7;
        color: #fff;
        text-decoration: none;
    }

    .btns a:active {
        background-color: #258a81;
    }
</style>

<body>
  <span class="pattern left">
    <img src="{{url('left.png')}}" alt="图片">
  </span>
  <span class="pattern right">
    <img src="{{url('right.png')}}" alt="图片">
  </span>
  <div id="app">
      <div class="content">
          <img class="logo" src="{{url('ankekan.jpg')}}" alt="安珂看看">
          <h1>安珂看看</h1>
          <div class="btns">
              <a href="{{$config->android_url}}">安卓版下载</a>
              <a href="{{$config->ios_url}}">IOS版下载</a>
          </div>
      </div>
  </div>
</body>

</html>