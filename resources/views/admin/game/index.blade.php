@extends('admin.base')

@section('content')
    <div class="layui-card">

       {{-- <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('content.game.create')
                    <a class="layui-btn layuiadmin-btn-useradmin" onclick="active.openLayerCustomForm('{{route('admin.game.create')}}','添加游戏', '80%', '70%');">添加游戏</a>

                @endcan
                @can('content.game.destroy')
                    <button class="layui-btn layuiadmin-btn-useradmin layui-btn-danger" id="listDelete">删除游戏</button>
                @endcan
            </div>
        </div>

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-inline">
                <label class="layui-form-label">开启/关闭:</label>
                <div class="layui-input-block">
                    <select name="is_open" class="form-control input-medium" id="is_open">
                        <option value="3">所有状态</option>
                        <option value="1">开启</option>
                        <option value="0">关闭</option>
                    </select>
                </div>
            </div>

            <div class="layui-inline">
                <button class="layui-btn layuiadmin-btn-useradmin" lay-submit lay-filter="LAY-user-front-search">
                    <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                </button>
            </div>
        </div>
--}}
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('content.game.edit')
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>编辑</a>
                    @endcan


                        @can('content.game.type')
                            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="type">游戏玩法</a>
                        @endcan

                        {{--@can('content.game.isuse')
                            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="isopen">
                                @{{# if(d.game_status === 1){ }}
                                <span>关闭</span>
                                @{{# } else { }}
                                <span>开启</span>
                                @{{# } }}
                            </a>
                        @endcan--}}
                    {{--@can('content.game.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                                class="layui-icon layui-icon-delete"></i>删除</a>
                    @endcan--}}
                </div>
            </script>
            <script type="text/html" id="game_status">
                @{{#  if(d.game_status === 1){ }}
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
                    , url: "{{ route('admin.game.data') }}" //数据接口
                    , where: {model: "game"}
                    , page: true //开启分页
                    , limit: 10
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        {checkbox: true, fixed: true}
                        , {field: 'id', title: '游戏ID', sort: true, width: 100}
                        , {field: 'game_name', title: '游戏名称'}
                        , {field: 'game_status', title: '游戏状态',  templet: '#game_status'}
                        , {field: 'start_time', title: '开始时间'}
                        , {field: 'end_time', title: '结束时间'}
                        , {field: 'color_limited_red', title: '彩种限红'}
                        , {field: 'single_bet_limit_red', title: '单注限红'}
                        , {field: 'sort', title: '排序'}
                        , {field: 'odds', title: '赔率'}
                        , {field: 'split_time', title: '封盘时间(秒)'}
                        , {field: 'min_bet_money', title: '最小投注'}
                        , {field: 'time_interval', title: '开奖时间间隔'}
                        , {field: 'created_at', title: '创建时间'}
                        , {fixed: 'right', title: '操作', width: 138, align: 'center', toolbar: '#options'}
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
                        layer.confirm('确认删除吗？', function (index) {
                            $.post("{{ route('admin.game.destroy') }}", {_method: 'delete', ids: [data.id]}, function (result) {
                                if (result.code == 0) {
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg, {icon: 6});
                            });
                        });
                    } else if (layEvent === 'edit') {
                        active.openLayerCustomForm('/admin/game/' + data.id + '/edit', '编辑游戏信息(ID:' + data.id + ')', '70%', '60%');
                    }else if(layEvent === 'isopen')
                    {
                        layer.confirm('确认关闭/开启该游戏吗？', function (index) {
                            $.post("{{ route('admin.game.isuse') }}", {
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
                    }else if (layEvent === 'type') {
                        active.openLayerNofullForm('/admin/game/' + data.id + '/game_type', '游戏玩法(ID:' + data.id + ')', '90%', '80%');
                    }
                });

                //按钮批量删除
                $("#listDelete").click(function () {
                    var ids = [];
                    var hasCheck = table.checkStatus('dataTable');
                    var hasCheckData = hasCheck.data;
                    if (hasCheckData.length > 0) {
                        $.each(hasCheckData, function (index, element) {
                            ids.push(element.id);
                        })
                    }
                    if (ids.length > 0) {
                        layer.confirm('确认删除吗？', function (index) {
                            $.post("{{ route('admin.game.destroy') }}", {_method: 'delete', ids: ids}, function (result) {
                                if (result.code == 0) {
                                    dataTable.reload();
                                }
                                layer.close(index);
                                layer.msg(result.msg, {icon: 6});
                            });
                        })
                    } else {
                        layer.msg('请选择删除项', {icon: 5});
                    }
                });

            })
        </script>
    @endcan
@endsection



