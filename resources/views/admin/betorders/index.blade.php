@extends('admin.base')

@section('content')

    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">注单号</label>
                    <div class="layui-input-block">
                        <input type="text" name="order" class="layui-input" id="order" placeholder="注单号">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">期号</label>
                    <div class="layui-input-block">
                        <input type="text" name="issue" class="layui-input" id="issue" placeholder="期号">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-block">
                        <input type="text" name="username" class="layui-input" id="username" placeholder="用户名">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">状态:</label>
                    <div class="layui-input-block">
                        <select name="open_status" class="form-control input-medium" id="open_status">
                            <option value="0">所有状态</option>
                            <option value="1">未中奖</option>
                            <option value="2">已中奖</option>
                            <option value="3">未开奖</option>
                            <option value="4">已开奖</option>
                            <option value="5">已撤单</option>
                            <option value="6">全部(不含撤单)</option>
                        </select>
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
                <div class="layui-btn-group">
                    @can('member.betorders.upuser')
                        @{{#  if(d.is_cancel === 1){ }}
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="del"><i
                                class="layui-icon layui-icon-edit"></i>撤单</a>
                        @{{#  } }}
                        @{{#  if(d.is_open === 0){ }}
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>编辑注单</a>
                        @{{#  } }}
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('member.betorders')
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
                    , url: "{{ route('admin.betorders.data') }}" //数据接口
                    , where: {model: "betorders"}
                    , page: true //开启分页
                    , limit: 20
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        //{checkbox: true, fixed: false},
                        {field: 'id', title: 'ID', sort: true},
//                        {field: 'order', title: '单号', sort: true},
                        {title: "用户", field: "username"},
                        {title: "订单号", field: "order"},
                        {title: "彩种", field: "game_name"},
                        {title: "期号", field: "issue"},
                        {title: "投注号码", field: "bet"},
 //                       {title: "玩法", field: "play"},
                        {title: "单注金额", field: "single_money"},
                        {title: "赔率", field: "odds"},
                        {title: "投注总额", field: "bet_money"},
                        {title: "中奖金额", field: "win_money"},
 //                      {title: "返点", field: "win_rebate"},
                        {title: "盈亏", field: "personal_profit_and_loss"},
                        {title: "来源", field: "origin"},
                        {title: "投注时间", field: "created_at"},
                        {fixed: 'right', field: 'pay_status', width: 160, title: '操作', align: 'center', toolbar: '#options'}
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
                    if (layEvent === 'del') {
                        layer.confirm('确认要撤销此下注订单吗？', function (index) {
                            $.post("{{ route('admin.betorders.upuser') }}", {
                                _method: 'put',
                                ids: [data.id]
                            }, function (result) {
                                if (result.code == 0) {
                                    success: location.reload()
                                }
                                layer.close(index);
                                layer.msg(result.msg, {icon: 6});
                            });
                        });
                    }else if (layEvent === 'edit') {
                        active.openLayerCustomForm('/admin/betorders/' + data.id + '/edit', '编辑游戏订单(ID:' + data.id + ')', '70%', '60%');
                    }
                });

            })
        </script>
    @endcan
@endsection
