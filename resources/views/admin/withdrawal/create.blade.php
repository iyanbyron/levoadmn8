@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.videobigclass.store')}}" method="post">
            @include('admin.videobigclass._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                title: [
                    /^[\S]/
                    , '标题不能为空'
                ],
                content: [
                    /^[\S]/
                    , '内容不能为空'
                ]
            });
        });
    </script>
@endsection
