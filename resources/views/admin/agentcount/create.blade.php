@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.push.store')}}" method="post">
            @include('admin.push._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                push_title: [
                    /^[\S]/
                    , '标题不能为空'
                ],
                push_content: [
                    /^[\S]/
                    , '内容不能为空'
                ]
            });

            form.on('radio(push_way)', function (data) {
                var val = data.value;
                if (val == 2) {
                    $("#push_uid_div").show();
                } else {
                    $("#push_uid_div").hide();
                }

            });

            form.on('select(push_jump_type)', function (data) {
                var val = data.value;
                //console.log(val);
                if (val == 2) {
                    $("#push_url_div").show();
                } else {
                    $("#push_url_div").hide();
                }

            });

        });
    </script>
@endsection
