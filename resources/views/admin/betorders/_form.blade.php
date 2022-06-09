{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">用户名</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="text" name="username" value="{{ $data->username ?? old('username') }}"
                class="layui-input" disabled>
    </div>
</div>



<div class="layui-form-item">
    <label for="" class="layui-form-label">游戏名称</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="text" name="game_name" value="{{ $data->game_name ?? old('game_name') }}" lay-verify="required" lay-vertype="tips"
               class="layui-input" disabled>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">注单号</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="email" name="order" value="{{$data->order??old('order')}}" placeholder="请输入Email"
               class="layui-input" disabled>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">投注号码</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="text" name="bet" value="{{$data->bet??old('bet')}}" placeholder="请输入手机号" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">投注金额</label>
    <div class="layui-input-inline" style="width: 45%;">
        <input type="text" name="single_money" value="{{$data->single_money??old('single_money')}}" placeholder="请输入收款帐号" class="layui-input">
    </div>
</div>


<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.betorders')}}">返 回</a>
    </div>
</div>
