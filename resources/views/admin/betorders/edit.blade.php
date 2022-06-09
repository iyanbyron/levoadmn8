@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.betorders.update',$data->id)}}" method="post">
            <input type="hidden" name="id" value="{{$data->id}}">
            {{method_field('put')}}
            @include('admin.betorders._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                username: [
                    /^[\S]{6,12}$/
                    , '用户名必须至少6到12字符'
                ],
                bet: [
                    /^[\S]{1,14}$/
                    , '投注金额必填，且不能出现空格'
                ]
            });
        });
    </script>
@endsection
