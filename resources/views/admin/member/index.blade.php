@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('member.user.create')
                    <a class="layui-btn layuiadmin-btn-useradmin"
                       onclick="active.openLayerCustomForm('{{route('admin.member.create')}}','添加用户/代理','96%','88%');">添加用户/代理</a>
                @endcan
            </div>
        </div>
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">用户名:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="username" placeholder="用户名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">真实姓名:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="actual_name" placeholder="真实姓名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">所有用户:</label>
                    <div class="layui-input-block">
                        <select name="user_types" class="form-control input-medium" id="user_types">
                            <option value="">所有用户</option>
                            <option value="1">会员</option>
                            <option value="2">总代理</option>
                            <option value="3">一级代理</option>
                            <option value="4">二级代理</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">开始时间:</label>
                    <div class="layui-input-block">
                        <input type="text" name="time_start" class="layui-input" id="time_start" placeholder="用户创建开始时间">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">结束时间:</label>
                    <div class="layui-input-block">
                        <input type="text" name="time_end" class="layui-input" id="time_end" placeholder="用户创建结束时间">
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
                    @can('member.user.edit')
                        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                                class="layui-icon layui-icon-edit"></i>修改</a>
                    @endcan

                    @can('member.user.isuse')
                        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                                class="layui-icon layui-icon-delete"></i>
                            @{{# if(d.status === 1){ }}
                            <span>停用</span>
                            @{{# } else { }}
                            <span>启用</span>
                            @{{# } }}
                        </a>
                    @endcan

                    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="bankcard"><i
                            class="layui-icon "></i>银行卡信息</a>
                </div>
            </script>
            <script type="text/html" id="is_use">
                @{{#  if(d.status === 1){ }}
                <span>正常</span>
                @{{#  } else { }}
                <span>停用</span>
                @{{#  } }}
            </script>
            <script type="text/html" id="online_status">
                @{{#  if(d.online_status === 1){ }}
                <span>在线</span>
                @{{#  } else { }}
                <span>离线</span>
                @{{#  } }}
            </script>


            <script type="text/html" id="user_type">
                @{{#  if(d.user_type === 1){ }}
                <span>会员</span>
                @{{#  } }}
                @{{#  if(d.user_type === 2){ }}
                <span>总代理</span>
                @{{#  } }}
                @{{#  if(d.user_type === 3){ }}
                <span>一级代理</span>
                @{{#  } }}
                @{{#  if(d.user_type === 4){ }}
                <span>二级代理</span>
                @{{#  } }}

            </script>
        </div>

    </div>
@endsection

@section('script')
    @can('member.user')
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
                    , url: "{{ route('admin.member.data') }}" //数据接口
                    , where: {model: "member"}
                    , page: true //开启分页
                    , limit: 10
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        // {field: 'id', title: 'id', sort: true}
                        {field: 'username', title: '用户名'}
                        , {field: 'actual_name', title: '真实姓名'}
                        , {field: 'user_type', title: '类型', templet: '#user_type'}
                        , {field: 'status', title: '状态', templet: '#is_use'}
                        , {field: 'recharge_today', title: '今日充值'}
                        , {field: 'withdraw_today', title: '今日提现'}
                        , {field: 'histor_recharge', title: '历史充值'}
                        , {field: 'histor_withdraw', title: '历史提现'}
                        , {field: 'superior', title: '上级用户'}
                        , {field: 'invitation_code', title: '邀请码'}
                        , {field: 'money', title: '余额'}
                        , {field: 'frozen_money', title: '冻结'}
                        , {field: 'code_amount', title: '打码额'}
                        //                       , {field: 'online_status', title: '在线状态',  templet: '#online_status'}
                        , {field: 'created_at', title: '注册时间'}
                        , {field: 'updated_at', title: '登陆时间'}
                        , {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#options'}
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
                        layer.confirm('确认停用/启用该用户吗？', function (index) {
                            $.post("{{ route('admin.member.isuse') }}", {
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
                    } else if (layEvent === 'edit') {
                        active.openLayerCustomForm('/admin/member/' + data.id + '/edit', '修改用户(ID:' + data.id + ')', '90%', '70%');
                    } else if (layEvent === 'bankcard') {
                        active.openLayerCustom('/admin/member/' + data.id + '/' + data.username + '/bankcard', '银行卡信息:' + data.username + '(ID:' + data.id + ')', '80%', '80%');

                    }
                });

            })
        </script>
    @endcan
@endsection
