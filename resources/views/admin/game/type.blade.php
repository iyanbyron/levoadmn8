@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('content.game.createtype')
                    <a class="layui-btn layuiadmin-btn-useradmin" onclick="active.openLayerCustomForm('{{route('admin.game.createtype')}}?id={{ $data->id ?? old('id') }}','添加玩法', '80%', '70%');">添加玩法</a>
                @endcan

            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                        @can('content.game.isuse')
                            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="isopen">
                                @{{# if(d.is_open === 1){ }}
                                <span>关闭</span>
                                @{{# } else { }}
                                <span>开启</span>
                                @{{# } }}
                            </a>
                        @endcan
                </div>
            </script>
            <script type="text/html" id="is_open">
                @{{#  if(d.is_open === 1){ }}
                <span>正常</span>
                @{{#  } else { }}
                <span>关闭</span>
                @{{#  } }}
            </script>
        </div>

    </div>
@endsection

@section('script')
    @can('content.game')
        <script>
            layui.use(['layer', 'table', 'form'], function () {
                var layer = layui.layer, form = layui.form, table = layui.table;

                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    , url: "{{ route('admin.game.game_type_data') }}" //数据接口
                   // , where: {model: "game"}
                    , where: {id: {{ $data->id ?? old('id') }}, game_name: "{{ $data->game_name ?? old('game_name') }}"}
                    , page: false //开启分页
                    , limit: 100
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        {field: 'game_name', title: '游戏名称'}
                        // , {field: 'id', title: '游戏ID', sort: true, width: 100}
                        , {field: 'game_id', title: '游戏id'}
                        , {field: 'game_type', title: '和值玩法', edit: 'text'}
                        , {field: 'bet_limit', title: '投注限额(点击修改)', edit: 'text'}
                        , {field: 'odds', title: '赔率(点击修改)', edit: 'text'}
                        , {field: 'type_v', title: '和值可能性(点击修改)', edit: 'text'}
                        , {field: 'is_open', title: '玩法状态',  templet: '#is_open'}
                        , {fixed: 'right', title: '操作', width: 80, align: 'center', toolbar: '#options'}
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
                   if(layEvent === 'isopen')
                    {
                        layer.confirm('确认关闭/开启该游戏玩法吗？', function (index) {
                            $.post("{{ route('admin.game.type_isuse') }}", {
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
                    }
                });

              //监听单元格编辑
                table.on('edit(dataTable)', function (obj) {
                    var value = obj.value //得到修改后的值
                        , data = obj.data //得到所在行所有键值
                        , field = obj.field; //得到字段
                    layer.confirm('确认要修改吗？', function (index) {
                        $.post("{{ route('admin.game.edittype') }}", {
                            _method: 'put',
                            fieldname: field,
                            id: data.id,
                            sortvalue: value
                        }, function (result) {
                            if (result.code == 0) {
                                //success: location.reload()
                                layer.msg(result.msg, {icon: 6});
                            } else {
                                layer.msg(result.msg, {icon: 5});
                            }
                            layer.close(index);
                        });
                    });
                    this.style.background = "red";
                    //layer.msg('[ID: '+ data.ID +'] ' + field + ' 字段更改为：'+ value);
                });


            })
        </script>
    @endcan
@endsection



