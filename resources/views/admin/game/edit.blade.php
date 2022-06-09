@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.game.update',$data->id)}}" method="post">
            <input type="hidden" name="id" value="{{$data->id}}">
            {{method_field('put')}}
            @include('admin.game._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                game_name: [
                    /^[\S]/
                    , '标题不能为空'
                ],
                color_limited_red: [
                    /^[\S]/
                    , '彩种限红不能为空'
                ]
            });
        });
    </script>
@endsection
