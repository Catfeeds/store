<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{url('css/quill.bubble.css')}}">
    <title>{{$article->title}}</title>
</head>

<body>
<div class="ql-bubble">
    <div class="ql-editor">
        {!! $article->content !!}
    </div>
</div>
</body>

</html>