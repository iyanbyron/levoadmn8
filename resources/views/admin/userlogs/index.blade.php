@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">

                    <div class="layui-inline">
                        <label class="layui-form-label">用户名:</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" class="layui-input" id="admin_id" placeholder="用户名">
                        </div>
                    </div>
                <div class="layui-inline">
                    <label class="layui-form-label">ip:</label>
                    <div class="layui-input-block">
                        <input type="text" name="login_ip" class="layui-input" id="admin_id" placeholder="ip">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">开始时间:</label>
                    <div class="layui-input-block">
                        <input type="text" name="time_start" class="layui-input" id="time_start" placeholder="开始时间">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">结束时间</label>
                    <div class="layui-input-block">
                        <input type="text" name="time_end" class="layui-input" id="time_end" placeholder="结束时间">
                    </div>
                </div>


                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-useradmin" lay-submit lay-filter="LAY-user-front-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
        </div>

    </div>
@endsection

@section('script')
    @can('logs.userlogs')
        <script>
            layui.use(['layer', 'table', 'form', 'laydate'], function () {
                var layer = layui.layer, form = layui.form, table = layui.table, laydate = layui.laydate;
                laydate.render({
                    elem: '#time_start'
                    , type: 'datetime'
                });
                laydate.render({
                    elem: '#time_end'
                    , type: 'datetime'
                });
                var time_start = $("input[name='time_start']").val();
                var time_end = $("input[name='time_end']").val();

                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    , url: "{{ route('admin.userlogs.data') }}" //数据接口
                    , where: {model: "userlogs"}
                    , page: true //开启分页
                    , limit: 10
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        {field: 'id', title: 'ID', sort: true, width: 80}
                        , {field: 'username', title: '用户名'}
                        , {field: 'login_ip', title: '	登陆ip'}
                        , {field: 'browser', title: '浏览器'}
                       // , {field: 'login_addr', title: '登录地址'}
                        , {field: 'login_domain', title: '登录域名'}
                        , {field: 'created_at', title: '登陆时间'}
                    ]]
                });

                //监听搜索
                form.on('submit(LAY-user-front-search)', function (data) {
                    var field = data.field;
                    //执行重载
                    table.reload('dataTable', {
                        where: field,
                        page: {curr: 1}
                    });
                });


            })
        </script>
    @endcan
@endsection
