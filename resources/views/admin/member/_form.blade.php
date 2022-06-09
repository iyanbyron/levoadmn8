{{csrf_field()}}

<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">用户名：</label>
            <div class="layui-input-block">
                <input type="text" name="username" @if ($user->id <> 0) readonly @endif   value="{{ $user->username ?? old('username') }}" class="layui-input" lay-verify="required">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">密码：</label>
            <div class="layui-input-block">
                <input type="text" name="password"
                       placeholder="密码,不修改请留空" class="layui-input" lay-verify="required">
            </div>
        </div>
    </div>
</div>


<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">手机：</label>
            <div class="layui-input-block">
                <input type="text" name="mobile" value="{{ $user->mobile ?? old('mobile') }}" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">QQ：</label>
            <div class="layui-input-block">
                <input type="text" name="qq" value="{{ $user->qq ?? old('qq') }}" class="layui-input">
            </div>
        </div>
    </div>
</div>


<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">姓名：</label>
            <div class="layui-input-block">
                <input type="text" name="actual_name" value="{{ $user->actual_name ?? old('actual_name') }}" class="layui-input" lay-verify="required">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">属于上级：</label>
            <div class="layui-input-block">
                <input type="text" name="superior" value="{{ $user->superior ?? old('superior') }}" class="layui-input" placeholder="请填写上级用户名">
            </div>
        </div>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">用户属性：</label>
            <div class="layui-input-block">
                <select name="user_type" id="user_type" class="field-pid" type="select" lay-filter="user_type">
                    <option value="1" @if ($user->user_type === 1) selected @endif>会员</option>
                    <option value="2" @if ($user->user_type === 2) selected @endif>总代理</option>
                    <option value="3" @if ($user->user_type === 3) selected @endif>一级代理</option>
                    <option value="4" @if ($user->user_type === 4) selected @endif>二级代理</option>
                </select>
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">当前状态：</label>
            <div class="layui-input-block">
                <input type="radio" value="1" title="启用" name="status" <?php if(isset($user) && $user->status == 1){?>checked<?php }?>>
                <input type="radio" value="0" title="停用" name="status" <?php if(isset($user) && $user->status == 0){?>checked<?php }?>>
                {{--  <input type="text"   readonly  value="@if ($user->status === 0) 停用 @else  启用 @endif" class="layui-input">--}}
            </div>
        </div>
    </div>
</div>


<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">资金密码：</label>
            <div class="layui-input-block">
                <input type="text" name=money_password" "{{ $user->money_password ?? old('money_password') }}"  placeholder="资金密码,不修改请留空" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">打码额：</label>
            <div class="layui-input-block">
                <input type="text" name="code_amount" value="{{ $user->code_amount ?? old('code_amount') }}" class="layui-input">
            </div>
        </div>
    </div>
</div>


<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.member')}}">返 回</a>
    </div>
</div>
