@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-block">
                        <input type="text" name="username" class="layui-input" id="username" placeholder="用户名">
                    </div>
                </div>


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
                {{--<div class="layui-btn-group">
                    @can('reports.usercount.edit')
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>编辑</a>
                    @endcan

                    @can('reports.usercount.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                                class="layui-icon layui-icon-delete"></i>删除</a>
                    @endcan
                </div>--}}
            </script>

        </div>

    </div>
@endsection

@section('script')
    @can('reports.usercount')
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
                    , url: "{{ route('admin.usercount.data') }}" //数据接口
                    , where: {superior_id:"{{$data->superior_id}}"}
                    , page: true //开启分页
                    , limit: 20
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                      /*  {checkbox: true, fixed: true}*/
                       /* , {field: 'id', title: 'ID', sort: true, width: 50}*/
                         {field: 'username', title: '用户名'}
                        , {field: 'sum_money', title: '总盈亏'}
                        , {field: 'bet_sum_money', title: '投注总额'}
                        , {field: 'charge_money', title: '充值金额'}
                        , {field: 'charge_num', title: '充值次数'}
                        , {field: 'withdrawal_money', title: '提现金额'}
                        , {field: 'withdrawal_num', title: '提现次数'}
                        , {field: 'activity_money', title: '活动费用'}
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



