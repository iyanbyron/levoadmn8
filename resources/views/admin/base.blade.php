<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LevoAdmin - 内页</title>
    <meta name="keywords" content="LevoAdmin - 内页">
    <meta name="description" content="LevoAdmin - 内页">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/static/common/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/common/style/admin.css" media="all">
</head>
<body>
<style type="text/css">
    .layadmin-carousel.layui-carousel > [carousel-item] > *, .layadmin-carousel.layui-carousel {
        background: #ffffff;
    }

    .layadmin-env {
        min-height: auto;
    }
</style>
<div class="layui-fluid">
    @yield('content')
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/echarts.min.js"></script>
<script src="/static/common/layui/layui.js"></script>
<script src="/static/admin/js/admin.js"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    layui.config({
        base: '/static/common/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['element', 'layer'], function () {
        var element = layui.element
            , layer = layui.layer;

        //错误提示
        @if(count($errors)>0)
        @foreach($errors->all() as $error)
        layer.msg("{{$error}}", {icon: 5});
        @break
        @endforeach
        @endif

        //信息提示
        @if(session('status'))
        layer.msg("{{session('status')}}", {icon: 6});
        @endif

    });

</script>
@yield('script')
</body>
</html>



