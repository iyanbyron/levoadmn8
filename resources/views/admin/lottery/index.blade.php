@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">彩种</label>
                    <div class="layui-input-block">
                        <input type="text" name="game_name" class="layui-input" id="game_name" placeholder="彩种">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">期号</label>
                    <div class="layui-input-block">
                        <input type="text" name="issue" class="layui-input" id="issue" placeholder="期号">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">日期</label>
                    <div class="layui-input-block">
                        <input type="text" name="time_start" class="layui-input" id="time_start" placeholder="开始时间">
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
                    @can('content.lottery.edit')
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>手动开奖</a>
                    @endcan


                </div>
            </script>

        </div>

    </div>
@endsection

@section('script')
    @can('content.lottery')
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
                    , url: "{{ route('admin.lottery.data') }}" //数据接口
                    , where: {model: "lottery"}
                    , page: true //开启分页
                    , limit: 15
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                         {field: 'game_name', title: '彩种'}
                        , {field: 'sort', title: '场次'}
                        , {field: 'issue', title: '期号'}
                        , {field: 'win_number', title: '开奖数据'}
                         , {field: 'sum_value', title: '和值'}
                        , {field: 'open_time', title: '开奖时间'}
                        , {field: 'bet_money', title: '总投注额'}
                        , {field: 'win_money', title: '中奖金额'}
                        , {field: 'is_open', title: '状态', templet: '#is_open'}
                        , {field: 'updated_at', title: '更新时间'}
                        , {fixed: 'right', title: '操作',  align: 'center', toolbar: '#options'}
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
                    if (layEvent === 'edit') {
                        active.openLayerCustomForm('/admin/lottery/' + data.id + '/edit', '手动开奖信息(ID:' + data.id + ')', '50%', '30%');
                    }
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



        <script type="text/html" id="is_open">
            @{{#  if(d.is_open === 1){ }}
            <span>已开奖</span>
            @{{#  } }}
            @{{#  if (d.is_open === 0) { }}
            <span>未开奖</span>
            @{{#  }   }}
        </script>



        </script>
    @endcan
@endsection



