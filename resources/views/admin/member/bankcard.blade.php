@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('member.user.create')
                    <a class="layui-btn layuiadmin-btn-useradmin" onclick="active.openLayerCustomForm('{{route('admin.member.bankcardcreate')}}?user_id={{ $data->user_id ?? old('user_id') }}','新增银行卡信息','96%','88%');">新增银行卡</a>
                @endcan
            </div>
        </div>


        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('member.user.edit')
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>修改</a>
                    @endcan

                    @can('member.user.isuse')
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                                class="layui-icon layui-icon-delete"></i> 删除</a>
                    @endcan

                </div>
            </script>

        </div>

    </div>
@endsection

@section('script')
    @can('member.user')
        <script>
            layui.use(['layer', 'table', 'form'], function () {
                var layer = layui.layer, form = layui.form, table = layui.table;

                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    , url: "{{ route('admin.member.bankcard_data') }}" //数据接口
                   // , where: {model: "member"}
                    , where: {user_id: {{ $data->user_id ?? old('user_id') }}, username: "{{ $data->username ?? old('username') }}"}
                    , page: false //开启分页
                    , limit: 50
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        {field: 'id', title: 'ID'}
                        , {field: 'username', title: '用户名'}
                        , {field: 'user_id', title: '用户id'}
                       // , {field: 'bank_code', title: '银行编号'}
                        , {field: 'bank_name', title: '银行名称'}
                        , {field: 'subbranch_name', title: '支行名称'}
                        , {field: 'bank_card_number', title: '银行卡号'}
                        , {field: 'account_name', title: '开户姓名'}
                        , {field: 'created_at', title: '创建日期'}
                        , {field: 'updated_at', title: '更新时间'}
                        , {fixed: 'right', title: '操作', width: 146, align: 'center', toolbar: '#options'}
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
                            $.post("{{ route('admin.member.bankcarddestroy') }}", {_method: 'delete', id: data.id}, function (result) {
                                if (result.code == 0) {
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg, {icon: 6});
                            });
                        });

                    } else if (layEvent === 'edit') {
                        active.openLayerCustomForm('/admin/member/' + data.id + '/bankcardedit', '修改用户(ID:' + data.id + ')', '90%', '70%');
                    } else if (layEvent === 'bankcard') {
                        active.openLayerCustom('/admin/member/' + data.id +'/'+ data.username+ '/bankcard', '银行卡信息:' + data.username + '(ID:' + data.id + ')', '80%', '80%');

                    }
                });

            })
        </script>
    @endcan
@endsection
