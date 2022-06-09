@extends('admin.from')

@section('content')

    <div class="layui-card-body">
        <table class="layui-table" style="table-layout: fixed;">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
            </colgroup>
            <tbody>

            <tr>
                <td class="set">路由</td>
                <td>{{$item['path']??''}}</td>
                <td class="set">IP地址</td>
                <td>{{$item['ip']??''}}</td>
            </tr>

            <tr>
                <td class="set">请求方式</td>
                <td>{{$item['method']??''}}</td>
                <td class="set">请求参数</td>
                <td id="showview">{{$item['input']??''}}</td>
            </tr>

            <tr>
                <td class="set">IP解析地址</td>
                <td>{{$ipdata['country'].' '.$ipdata['region'].' '.$ipdata['city'].' '.$ipdata['county']}}</td>
                <td class="set">运营商</td>
                <td> {{$ipdata['isp']??''}} （{{$ipdata['isp_id']}}）</td>
            </tr>

            <tr>
                <td colspan="4"></td>
            </tr>

            <tr>
                <td class="set">Agent</td>
                <td colspan="3">{{$item['agent']??''}}</td>
            </tr>

            <tr>
                <td class="set">操作系统</td>
                <td>{{$arr['system_name']??''}} {{$arr['system_version']??''}}</td>
                <td class="set">浏览器</td>
                <td>{{$arr['browser_name']??''}} {{$arr['browser_version']??''}}</td>
            </tr>

            <tr>
                <td class="set">设备名称</td>
                <td>{{$arr['device_name']??''}}</td>
                <td class="set">语言</td>
                <td>{{$arr['languages']??''}}</td>
            </tr>

            <tr>
                <td class="set">是否机械人</td>
                <td>{{$arr['isRobot']?'是':'否'}}</td>
                <td class="set">机械人名称</td>
                <td>{{$arr['Robot_name']??''}}</td>
            </tr>

            <tr>
                <td colspan="4"></td>
            </tr>

            <tr>
                <td class="set">用户</td>
                <td>
                    {{$item['user']['name']??'未知'}} (后台)
                </td>
                <td class="set"> 请求时间</td>
                <td> {{$item['created_at']??''}} </td>
            </tr>

            </tbody>
        </table>

    </div>
@endsection

@section('script')
    <style>
        .layui-table tr .set {
            background-color: #f2f2f2;
        }

        .layui-table tr td {
            word-wrap: break-word;
        }
    </style>
    <script>
        layui.use(['jquery', 'layer'], function () {
            var $ = layui.$, layer = layui.layer;
            $("#showview").on('click', function () {
                var text = $(this).text();

                var html = '<pre class="layui-code"><code>' + '\n'
                    + JSON.stringify(JSON.parse(text), null, 2) + '\n'
                    + '</pre></code>';

                layer.open({
                    title: 'code'
                    , type: 1
                    , anim: 2
                    , shadeClose: true
                    , skin: 'layui-layer-rim', //加上边框
                    area: ['50%', '50%'], //宽高
                    content: html
                });
            });
        });

    </script>
@endsection
