@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.label.store')}}" method="post">
            @include('admin.label._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                label_name: [
                    /^[\S]/
                    , '名称不能为空'
                ]
            });
        });
    </script>
@endsection
