{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">用户名</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="text" name="username" value="{{ $user->username ?? old('username') }}" lay-verify="username"
               lay-vertype="tips" placeholder="请输入用户名" class="layui-input">
    </div>
</div>


<div class="layui-form-item">
    <label for="" class="layui-form-label">昵称</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="text" name="name" value="{{ $user->name ?? old('name') }}" lay-verify="required" lay-vertype="tips"
               placeholder="请输入昵称" class="layui-input">
    </div>
</div>


<div class="layui-form-item">
    <label for="" class="layui-form-label">密码</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="password" name="password" placeholder="请输入密码" lay-verify="pass" lay-vertype="tips"
               class="layui-input">
        @if(isset($user))
            <div class="layui-form-mid layui-word-aux">不修改密码则留空</div>
        @endif
    </div>

</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">确认密码</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="password" name="password_confirmation" placeholder="再次输入密码" lay-verify="pass" lay-vertype="tips"
               class="layui-input">
    </div>

</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.user')}}">返 回</a>
    </div>
</div>
