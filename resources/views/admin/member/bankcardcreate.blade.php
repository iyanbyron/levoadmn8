@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.member.bankcardstore')}}" method="post">
            @include('admin.member._bankcardform')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                username: [
                    /^[\S]/
                    , '用户名必填'
                ],
                user_id: [
                    /^[\S]/
                    , '用户id必填'
                ]
            });
        });
    </script>
@endsection
