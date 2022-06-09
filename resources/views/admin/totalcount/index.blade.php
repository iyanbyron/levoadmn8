@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">


                <div class="layui-inline">
                    <label class="layui-form-label">开始时间</label>
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
            <script type="text/html" id="options">

            </script>

        </div>

    </div>
@endsection

@section('script')
    @can('reports.totalcount')
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
                    , url: "{{ route('admin.totalcount.data') }}" //数据接口
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                      /*  {checkbox: true, fixed: true}*/
                         {field: 'date_time', title: '日期'}
                        , {field: 'money', title: '余额'}
                        , {field: 'profit_and_loss', title: '总盈亏'}
                        , {field: 'recharge', title: '充值金额'}
			, {field: 'num_peo', title: '充值人数'}
                        , {field: 'pen_count', title: '充值次数'}
                        , {field: 'first_charge', title: '首充'}
                        , {field: 'withdraw', title: '提现金额'}
                       // , {fixed: 'right', title: '操作', width: 140, align: 'center', toolbar: '#options'}
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

                ///监听工具条
                table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        , layEvent = obj.event; //获得 lay-event 对应的值
                });

            })
        </script>
    @endcan
@endsection



