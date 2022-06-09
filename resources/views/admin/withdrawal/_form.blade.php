{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">订单号</label>
    <div class="layui-input-block">
        <input type="text" name="withdraw_order" value="{{ $data->withdraw_order ?? old('withdraw_order') }}" lay-verify="required" lay-vertype="tips"
               placeholder="订单号" class="layui-input" readonly>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">用户名</label>
    <div class="layui-input-block">
        <input type="text" name="username" value="{{ $data->username ?? old('username') }}" lay-verify="required" lay-vertype="tips"
               placeholder="用户名" class="layui-input"  readonly>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">提现金额</label>
    <div class="layui-input-block">
        <input type="text" name="amount" value="{{ $data->amount ?? old('amount') }}" lay-verify="required" lay-vertype="tips"
               placeholder="提现金额" class="layui-input"  readonly>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">拒绝理由</label>
    <div class="layui-input-block">
        <textarea name="remark" rows="7" class="layui-textarea" lay-verify="required" lay-vertype="tips"
                  placeholder="拒绝理由"> {{ $data->remark ?? old('remark') }}</textarea>
    </div>
</div>


<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.withdrawal')}}">返 回</a>
    </div>
</div>
