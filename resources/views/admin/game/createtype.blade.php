@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.game.storetype')}}" method="post">
            @include('admin.game._formtype')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                game_id: [
                    /^[\S]/
                    , '游戏名id不能为空'
                ],
                game_type: [
                    /^[\S]/
                    , '和值玩法不能为空'
                ]
            });
        });
    </script>
@endsection
