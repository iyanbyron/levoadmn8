<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LevoAdmin 后台管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/common/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/common/style/admin.css" media="all">
    <link rel="stylesheet" href="/static/common/style/login.css" media="all">
    <style>
        .layadmin-user-login-main {
            background-color: #fff;
        }

        .layadmin-user-login {
            position: absolute;
            right: 0;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="layadmin-user-login layadmin-user-display-show">
    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2><img src="{{URL::asset('/images/logo.svg')}}"/> LevoAdmin</h2>
            <p>LevoAdmin 系统登录</p>
        </div>
        @yield('content')
    </div>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/static/common/layui/layui.js"></script>
<script>

    layui.config({
        base: '/static/common/' //静态资源所在路径
    }).extend({
        sliderVerify: 'plugins/sliderVerify/sliderVerify'
    }).use(['layer', 'sliderVerify', 'form'], function () {
        var layer = layui.layer, form = layui.form, sliderVerify = layui.sliderVerify;
        var slider = sliderVerify.render({
            elem: '#slider',
            onOk: function () {//当验证通过回调
                layer.msg("滑块验证通过");
            }
        });
        //监听提交
        form.on('submit(formDemo)', function (data) {
            if (!slider.isOk()) {
                layer.msg("请先通过滑块验证");
                return false;
            }
        });
        //表单提示信息
        @if(count($errors)>0)
        @foreach($errors->all() as $error)
        layer.msg("{{$error}}", {icon: 5});
        @break
        @endforeach
        @endif

        //正确提示
        @if(session('success'))
        layer.msg("{{session('success')}}", {icon: 6});
        @endif

    })
</script>
</body>
</html>
