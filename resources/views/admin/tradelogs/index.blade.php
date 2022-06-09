@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">

            <div class="layui-inline">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block">
                    <input type="text" name="username" class="layui-input" id="username" placeholder="用户名">
                </div>
            </div>


            <div class="layui-inline">
                <label class="layui-form-label">类型:</label>
                <div class="layui-input-block">
                    <select name="type" class="form-control input-medium" id="type">
                        <option value="0">所有状态</option>
                        <option value="1">充值</option>
                        <option value="2">提现</option>
                        <option value="3">投注(会员投注)</option>
                        <option value="4">开奖</option>
                        <option value="5">活动</option>
                        <option value="6">提现中</option>
                        <option value="7">提现失败</option>
                        <option value="8">提现成功</option>
                        <option value="11">投注(撤单)</option>
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
                <label class="layui-form-label">操作人</label>
                <div class="layui-input-block">
                    <input type="text" name="operator" class="layui-input" id="operator" placeholder="操作人">
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
    </div>
    <script type="text/html" id="types">
        @{{#  if(d.type === 1){ }}
        <span>充值</span>
        @{{#  } }}
        @{{#  if(d.type === 2){ }}
        <span>提现</span>
        @{{#  } }}
        @{{#  if(d.type === 3){ }}
        <span>投注(会员投注)</span>
        @{{#  } }}
        @{{#  if(d.type === 4){ }}
        <span>开奖</span>
        @{{#  } }}
        @{{#  if(d.type === 5){ }}
        <span>活动</span>
        @{{#  } }}
        @{{#  if(d.type === 6){ }}
        <span>提现中</span>
        @{{#  } }}
        @{{#  if(d.type === 7){ }}
        <span>提现失败</span>
        @{{#  } }}
        @{{#  if(d.type === 8){ }}
        <span>提现成功</span>
        @{{#  } }}
        @{{#  if(d.type === 11){ }}
        <span>投注(撤单)</span>
        @{{#  } }}
        @{{#  if(d.type ===0){ }}
        <span>---</span>
        @{{#  } }}
    </script>
    </div>
@endsection

@section('script')
    @can('member.tradelogs')
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
                    , url: "{{ route('admin.tradelogs.data') }}" //数据接口
                    , where: {model: "member"}
                    , page: true //开启分页
                    , limit: 15
                    , id: 'dataTable'
                    , initSort: {
                        field: 'id' //排序字段，对应 cols 设定的各字段名
                        , type: 'desc' //排序方式  asc: 升序、desc: 降序、null: 默认排序
                    }
                    , cols: [[ //表头
                        //{field: 'id', title: 'ID', sort: true, width: 80}
                        {field: 'order_num', title: '单号'}
                        , {field: 'username', title: '用户名'}
                        , {field: 'actual_name', title: '姓名'}
                        , {field: 'type', title: '类型', templet: '#types'}
                        , {field: 'game_name', title: '游戏'}
                        , {field: 'play', title: '玩法'}
                        , {field: 'issue', title: '期号'}
                        , {field: 'bet', title: '投注号码'}
                        , {field: 'bet_money', title: '流动金额'}
                        , {field: 'money', title: '当前余额'}
                        , {field: 'operator', title: '操作人'}
                        , {field: 'remark', title: '备注'}
                        , {field: 'created_at', title: '时间'}
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


            })
        </script>
    @endcan
@endsection
