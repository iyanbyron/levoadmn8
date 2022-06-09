@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form  layui-card-header layuiadmin-card-header-auto" lay-filter="layadmin-userfront-formlist">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="user_id" id="user_id" lay-search>
                            <option value="">User</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="method" id="method" lay-search>
                            <option value="">Method</option>
                            @foreach($methods as $method)
                                <option value="{{$method}}">{{$method}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

{{--                <div class="layui-inline">--}}
{{--                    <div class="layui-input-inline">--}}
{{--                        <input type="text" name="path" id="path" placeholder="Path" class="layui-input">--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="ip" id="ip" placeholder="Ip" class="layui-input">
                    </div>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-useradmin" id="searchBtn" lay-submit="" lay-filter="searchBtn">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
{{--            <div class="layui-btn-group " style="padding-bottom: 10px;">--}}
{{--                @can('system.operation.destroy')--}}
{{--                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删除</button>--}}
{{--                @endcan--}}
{{--            </div>--}}
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
{{--                <div class="layui-btn-group">--}}
{{--                    <a class="layui-btn layui-btn-sm" lay-event="show">查看</a>--}}
{{--                    @can('system.operation.destroy')--}}
{{--                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>--}}
{{--                    @endcan--}}
{{--                </div>--}}
            </script>
            <script type="text/html" id="user">
                @{{d.user?d.user.name:''}} (后台)
            </script>
            <script type="text/html" id="mmethod">
                <span class="layui-btn layui-btn-xs" style="background-color: @{{d.method_color}};">@{{d.method}}</span>
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('system.operation')
        <script>
            layui.use(['layer', 'table', 'laydate', 'form'], function () {
                var layer = layui.layer, table = layui.table, form = layui.form;
                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    , url: "{{ route('admin.operation.data') }}" //数据接口
                    , page: true //开启分页
                    , limit: 15
                    , cols: [[ //表头
                        {checkbox: true, fixed: true}
                        , {field: 'id', title: 'ID', sort: true, width: 80}
                        , {field: 'name', title: 'User', templet: "#user"}
                        , {field: 'method', title: 'Method', templet: "#mmethod", width: 90}
                        // , {field: 'path', title: 'Path'}
                        , {field: 'ip', title: 'Ip'}
                        // , {field: 'input', title: 'Input', event: 'openInput'}
                        , {field: 'created_at', title: '创建时间'}
                        // , {fixed: 'right', title: '操作', width: 120, align: 'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        , layEvent = obj.event; //获得 lay-event 对应的值
                    if (layEvent === 'del') {
                        layer.confirm('确认删除吗？', function (index) {
                            $.post("{{ route('admin.operation.destroy') }}", {_method: 'delete', ids: [data.id]}, function (result) {
                                if (result.code == 0) {
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        });
                    } else if (layEvent === 'openInput') {
                        return false;
                        layer.msg(data.input);
                    } else if (layEvent === 'show') {
                        active.openLayerForm('/admin/operation/' + data.id + '/show', '详情', {'btn': false, 'width': '70%', 'height': '90%'});
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
                            $.post("{{ route('admin.operation.destroy') }}", {_method: 'delete', ids: ids}, function (result) {
                                if (result.code == 0) {
                                    dataTable.reload();
                                }
                                layer.close(index);
                                layer.msg(result.msg,);
                            });
                        })
                    } else {
                        layer.msg('请选择删除项');
                    }
                });

                //搜索
                form.on('submit(searchBtn)', function (data) {
                    dataTable.reload({
                        where: data.field,
                        page: {curr: 1}
                    })
                });
            })
        </script>
    @endcan
@endsection
