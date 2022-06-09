@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.version.store')}}" method="post">
            @include('admin.version._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                ver_name: [
                    /^[\S]/
                    , '版本名称不能为空'
                ],
                ver_title: [
                    /^[\S]/
                    , '更新标题不能为空'
                ]
            });
        });
    </script>
@endsection
