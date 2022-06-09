@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.orders.store')}}" method="post">
            @include('admin.orders._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                userename: [
                    /^[\S]/
                    , '用户名'
                ]
            });
        });
        function searchUser(obj) {
            if (obj.value !== '' && obj.value.indexOf(",") == -1) {
                var objectModel = {};
                objectModel['username'] =obj.value;
                $.ajax({
                    dataType: "json",
                    url: "{{ route('admin.orders.getuser') }}",
                    type: "post",
                    data: objectModel,
                    timeout: 30000,
                    success: function (data) {
                        if (data.status=="success") {
                           // layer.msg(data.data.actual_name, {icon: 6});
                            //$("#actual_name").val(res.file_name)
                            $("#actual_name").val(data.data.actual_name ? data.data.actual_name : '无');
                            $("#money").val(data.data.money ? data.data.money : 0);
                            $("#is_agent").val(data.data.is_agent ? data.data.is_agent : '未知');
                        } else {
                            layer.msg(data.msg, {icon: 6});
                        }
                    }
                });
            }

        }
    </script>
@endsection
