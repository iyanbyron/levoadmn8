@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('config.paychannel.create')
                    <a class="layui-btn layuiadmin-btn-useradmin" onclick="active.openLayerCustomForm('{{route('admin.paychannel.create')}}','添加支付渠道','95%','90%');">添加渠道</a>
                @endcan
                @can('config.paychannel.destroy')
                    <button class="layui-btn layuiadmin-btn-useradmin layui-btn-danger" id="listDelete">删除渠道</button>
                @endcan
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('config.paychannel.edit')
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>编辑</a>
                    @endcan

                    @can('config.paychannel.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                                class="layui-icon layui-icon-delete"></i>删除</a>
                    @endcan
                </div>
            </script>

        </div>

    </div>
@endsection

@section('script')
    @can('config.paychannel')
        <script>
            layui.use(['layer', 'table', 'form'], function () {
                var layer = layui.layer, form = layui.form, table = layui.table;

                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    , url: "{{ route('admin.paychannel.data') }}" //数据接口
                    , where: {model: "paychannel"}
                    , page: true //开启分页
                    , limit: 10
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        {checkbox: true, fixed: true}
                        , {field: 'id', title: 'ID', sort: true}
                        , {field: 'chann_title', title: '渠道名称'}
                        , {field: 'mch_id', title: '商户id'}
                        , {field: 'appid', title: 'appid'}
                        , {field: 'pay_title', title: '支付名称'}
                        , {field: 'pay_type', title: '支付类型'}
                        , {field: "pay_is_rend", title: "小数金额", templet: "#pay_is_rend"}
                        , {field: "is_open", title: "状态", templet: "#is_open"}
                        , {field: 'updated_at', title: '时间'}
                        , {fixed: 'right', title: '操作', width: 140, align: 'center', toolbar: '#options'}
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
                            $.post("{{ route('admin.paychannel.destroy') }}", {_method: 'delete', ids: [data.id]}, function (result) {
                                if (result.code == 0) {
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg, {icon: 6});
                            });
                        });
                    } else if (layEvent === 'edit') {
                        active.openLayerCustomForm('/admin/paychannel/' + data.id + '/edit', '编辑小类信息(ID:' + data.id + ')', '95%', '90%');
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
                            $.post("{{ route('admin.paychannel.destroy') }}", {_method: 'delete', ids: ids}, function (result) {
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
        <script type="text/html" id="is_open">
            @{{#  if(d.is_open === 0){ }}
            <span>关闭</span>
            @{{#  } }}
            @{{#  if(d.is_open === 1){ }}
            <span style="color: #1E9FFF">打开</span>
            @{{#  } }}
        </script>

        <script type="text/html" id="pay_is_rend">
            @{{#  if(d.pay_is_rend === 0){ }}
            <span>否</span>
            @{{#  } }}
            @{{#  if(d.pay_is_rend === 1){ }}
            <span style="color: #1E9FFF">是</span>
            @{{#  } }}
        </script>
    @endcan
@endsection



