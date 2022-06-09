{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">用户名</label>
    <div class="layui-input-inline">
        <input type="hidden" name="user_type" value="1">
        <input type="text" name="username" value="{{ $user->username ?? old('username') }}" lay-verify="username"
               lay-vertype="tips" placeholder="请输入用户名" class="layui-input">
    </div>
</div>

{{--<div class="layui-form-item">
    <label for="" class="layui-form-label">用户级别</label>
    <div class="layui-input-inline" style="width: 45%;">
        <select name="tid" class="field-pid " type="select" lay-verify="" lay-filter="pid">
            <option value="">请选择</option>
            @foreach ($admin_type_list as $type)
                <option value=" {{ $type->id }}"  @if ($type->id === $user->tid) selected @endif>{{ $type->name }}</option>
            @endforeach
        </select>
    </div>
</div>--}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">昵称</label>
    <div class="layui-input-inline">
        <input type="text" name="name" value="{{ $user->name ?? old('name') }}" lay-verify="required" lay-vertype="tips"
               placeholder="请输入昵称" class="layui-input">
    </div>
</div>


<div class="layui-form-item">
    <label for="" class="layui-form-label">百分比</label>
    <div class="layui-input-block">
        <input type="text" name="agent_percent" value="{{ $user->agent_percent ?? old('agent_percent') }}" lay-vertype="tips"
               placeholder="分成百分比:如：0.8,不填默认为0.9" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">扣量数</label>
    <div class="layui-input-block">
        <input type="text" name="agent_deduct_num" value="{{ $user->agent_deduct_num ?? old('agent_deduct_num') }}" lay-vertype="tips"
               placeholder="代理每多少个订单扣出一个订单:如：12,不填默认为10" class="layui-input">
    </div>
</div>

@if(isset($user->after_days))
    <div class="layui-form-item">
        <div class="layui-row layui-col-space5">
            <label for="" class="layui-form-label">超过天数</label>
            <div class="layui-col-md4">
                <div class="layui-input-inline">
                    <input type="text" name="after_days" value="{{ $user->after_days ?? old('after_days') }}" placeholder="用户注册超过多少天后充值不计入代理收入,默认为10" lay-verify="after_days" lay-vertype="tips"
                           class="layui-input">
                </div>
            </div>
            <div class="layui-col-md8">
                <div class="layui-form-mid layui-word-aux">注册多少天后充值不计入代理收入</div>
            </div>
        </div>
    </div>
@else
    <div class="layui-form-item">
        <label for="" class="layui-form-label">超过天数</label>
        <div class="layui-input-block">
            <input type="text" name="after_days" value="{{ $user->after_days ?? old('after_days') }}" placeholder="用户注册超过多少天后充值不计入代理收入,默认为10" class="layui-input">
        </div>
    </div>
@endif


<div class="layui-form-item">
    <div class="layui-row layui-col-space5">
        <label for="" class="layui-form-label">密码</label>
        <div class="layui-col-md4">
            <div class="layui-input-inline">
                <input type="password" name="password" placeholder="请输入密码" lay-verify="pass" lay-vertype="tips"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-col-md8">
            @if(isset($user->username))
                <div class="layui-form-mid layui-word-aux">不修改密码则留空</div>
            @endif
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">确认密码</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="password" name="password_confirmation" placeholder="请输入密码" lay-verify="pass" lay-vertype="tips"
               class="layui-input">
    </div>

</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">备注说明</label>
    <div class="layui-input-block">
        <textarea name="remark" rows="3" class="layui-textarea" lay-vertype="tips"
                  placeholder="备注信息"> {{ $user->remark ?? old('remark') }}</textarea>
    </div>
</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.user')}}">返 回</a>
    </div>
</div>
