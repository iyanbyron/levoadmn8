@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-block">
                        <input type="text" name="username" class="layui-input" id="username" placeholder="请输入用户名">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">提现状态:</label>
                    <div class="layui-input-block">
                        <select name="is_status" class="form-control input-medium" id="is_status">
                            <option value="">全部</option>
                            <option value="0">提现中</option>
                            <option value="1">提现成功</option>
                            <option value="2">已拒绝</option>
                        </select>
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">更新时间</label>
                    <div class="layui-input-block">
                        <input type="text" name="time_start" class="layui-input" id="time_start" placeholder="更新时间">
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
                @{{# if(d.status === 0){ }}
                <div class="layui-btn-group">
                    @can('funds.withdrawal.agreepay')
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="agreepay"><i
                                class="layui-icon layui-icon-edit"></i>
                            <span>同意打款</span>
                        </a>
                    @endcan
                    @can('funds.withdrawal.refusepay')
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="refusepay"><i
                                class="layui-icon layui-icon-edit"></i>
                            <span> 拒绝驳回</span>
                        </a>
                    @endcan
                </div>

                @{{# } }}
            </script>
            <script type="text/html" id="tostatus">
                @{{#  if(d.status === 0){ }}
                <span style="color: #555555">提现中</span>
                @{{#  } }}
                @{{#  if(d.status === 2){ }}
                <span style="color: red">已拒绝</span>
                @{{#  } }}
                @{{#  if(d.status === 1){ }}
                <span style="color: green">提现成功</span>
                @{{#  } }}

            </script>


        </div>

    </div>
@endsection

@section('script')
    @can('funds.withdrawal')
        <script>
            layui.use(['layer', 'table', 'form', 'laydate'], function () {
                var layer = layui.layer, form = layui.form, table = layui.table, laydate = layui.laydate;
                laydate.render({
                    elem: '#time_start'
                    , type: 'datetime'
                });

                var time_start = $("input[name='time_start']").val();

                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    , url: "{{ route('admin.withdrawal.data') }}" //数据接口
                    , where: {model: "withdrawal"}
                    , page: true //开启分页
                    , limit: 10
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        //{title: "订单号", field: "withdraw_order", align: "center"},
                        {title: "用户名", field: "username", align: "center", width: 120},
                        {title: "姓名", field: "account_name", align: "center", width: 120},
                        {title: "银行名称", field: "bank_name", align: "center", width: 160},
                        {title: "支行名称", field: "subbranch_name", align: "center", width: 160},
                        {title: "银行卡号", field: "bank_card_number", align: "center", width: 200},
                        {
                            title: "提现金额",
                            field: "amount",
                            align: "center",
                            width: 130,
                            style: "color:red;font-size:16px"
                        },
                        {title: "提现状态", field: "status", templet: '#tostatus', align: "center"},
                        {title: "备注", align: "center"},
                        {title: '操作人', field: 'operator', align: "center"},
                        {title: '创建时间', field: 'created_at', align: "center"},
                        {title: '更新时间', field: 'updated_at', align: "center"},
                        {title: '操作', fixed: 'right', width: 190, align: 'center', toolbar: '#options'}
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

                //监听工具条
                table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        , layEvent = obj.event; //获得 lay-event 对应的值
                    if (layEvent === 'refusepay') {
                        active.openLayerCustomForm('/admin/withdrawal/' + data.id + '/edit', '驳回拒绝打款(ID:' + data.id + ')', '90%', '70%');
                        /* layer.confirm('确认驳回拒绝打款吗？', function (index) {
                              $.post("{{route('admin.withdrawal.refusepay') }}", {
                                _method: 'put',
                                id: data.id
                            }, function (result) {
                                if (result.code == 0) {
                                    success: location.reload()
                                }
                                layer.close(index);
                                layer.msg(result.msg, {icon: 6});
                            });
                        });*/
                    } else if (layEvent === 'agreepay') {
                        layer.confirm('你确认同意打款吗？', function (index) {
                            $.post("{{ route('admin.withdrawal.agreepay') }}", {
                                _method: 'put',
                                id: data.id
                            }, function (result) {
                                if (result.code == 0) {
                                    success: location.reload()
                                }
                                layer.close(index);
                                layer.msg(result.msg, {icon: 6});
                            });
                        });
                    }
                });

            })
        </script>
    @endcan
@endsection



