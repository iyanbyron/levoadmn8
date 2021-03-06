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
{{--                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">??????</button>--}}
{{--                @endcan--}}
{{--            </div>--}}
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
{{--                <div class="layui-btn-group">--}}
{{--                    <a class="layui-btn layui-btn-sm" lay-event="show">??????</a>--}}
{{--                    @can('system.operation.destroy')--}}
{{--                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">??????</a>--}}
{{--                    @endcan--}}
{{--                </div>--}}
            </script>
            <script type="text/html" id="user">
                @{{d.user?d.user.name:''}} (??????)
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
                //?????????????????????
                var dataTable = table.render({
                    elem: '#dataTable'
                    , url: "{{ route('admin.operation.data') }}" //????????????
                    , page: true //????????????
                    , limit: 15
                    , cols: [[ //??????
                        {checkbox: true, fixed: true}
                        , {field: 'id', title: 'ID', sort: true, width: 80}
                        , {field: 'name', title: 'User', templet: "#user"}
                        , {field: 'method', title: 'Method', templet: "#mmethod", width: 90}
                        // , {field: 'path', title: 'Path'}
                        , {field: 'ip', title: 'Ip'}
                        // , {field: 'input', title: 'Input', event: 'openInput'}
                        , {field: 'created_at', title: '????????????'}
                        // , {fixed: 'right', title: '??????', width: 120, align: 'center', toolbar: '#options'}
                    ]]
                });

                //???????????????
                table.on('tool(dataTable)', function (obj) { //??????tool????????????????????????dataTable???table????????????????????? lay-filter="????????????"
                    var data = obj.data //?????????????????????
                        , layEvent = obj.event; //?????? lay-event ????????????
                    if (layEvent === 'del') {
                        layer.confirm('??????????????????', function (index) {
                            $.post("{{ route('admin.operation.destroy') }}", {_method: 'delete', ids: [data.id]}, function (result) {
                                if (result.code == 0) {
                                    obj.del(); //??????????????????tr??????DOM??????
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        });
                    } else if (layEvent === 'openInput') {
                        return false;
                        layer.msg(data.input);
                    } else if (layEvent === 'show') {
                        active.openLayerForm('/admin/operation/' + data.id + '/show', '??????', {'btn': false, 'width': '70%', 'height': '90%'});
                    }
                });

                //??????????????????
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
                        layer.confirm('??????????????????', function (index) {
                            $.post("{{ route('admin.operation.destroy') }}", {_method: 'delete', ids: ids}, function (result) {
                                if (result.code == 0) {
                                    dataTable.reload();
                                }
                                layer.close(index);
                                layer.msg(result.msg,);
                            });
                        })
                    } else {
                        layer.msg('??????????????????');
                    }
                });

                //??????
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
