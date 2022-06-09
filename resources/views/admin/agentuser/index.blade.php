@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('system.agentuser.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('system.agentuser.create')
                    <a class="layui-btn layui-btn-sm" onclick="active.openLayerCustomForm('{{route('admin.agentuser.create')}}','添加代理','100%','55%');">添加代理</a>
                @endcan
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('system.agentuser.create')
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>编辑</a>
                    @endcan
                    @can('system.agentuser.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                                class="layui-icon layui-icon-delete"></i>删除</a>
                    @endcan
                </div>
            </script>
        </div>

    </div>
@endsection

@section('script')
    @can('system.agentuser')
        <script>
            layui.use(['layer', 'table', 'form'], function () {
                var layer = layui.layer, form = layui.form, table = layui.table;

                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    , url: "{{ route('admin.agentuser.data') }}" //数据接口
                    , where: {model: "adminuser"}
                    , page: true //开启分页
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'asc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        {checkbox: true, fixed: true}
                        , {field: 'id', title: 'ID', sort: true, width: 80}
                        , {field: 'username', title: '用户名', width: 80}
                        , {field: 'name', title: '昵称', width: 90}
                        , {field: 'agent_percent', title: '分成比例', width: 90}
                        , {field: 'agent_deduct_num', title: '扣单数', width: 80}
                        , {field: 'after_days', title: '超过天数', width: 90}
                        , {field: 'remark', title: '备注'}
                        , {field: 'created_at', title: '创建时间'}
                        , {fixed: 'right', title: '操作', width: 140, align: 'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        , layEvent = obj.event; //获得 lay-event 对应的值
                    if (layEvent === 'del') {
                        layer.confirm('确认删除吗？', function (index) {
                            $.post("{{ route('admin.agentuser.destroy') }}", {_method: 'delete', ids: [data.id]}, function (result) {
                                if (result.code == 0) {
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg, {icon: 6});
                            });
                        });
                    } else if (layEvent === 'edit') {
                        active.openLayerCustomForm('/admin/agentuser/' + data.id + '/edit', '更新代理', '100%', '55%');
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
                            $.post("{{ route('admin.agentuser.destroy') }}", {_method: 'delete', ids: ids}, function (result) {
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



