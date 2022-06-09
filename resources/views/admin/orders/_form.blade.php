{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">用户名</label>
    <div class="layui-input-block">
        <input type="text" onblur="searchUser(this)"   name="username" value="{{ $data->username ?? old('username') }}" lay-verify="required" lay-vertype="tips"
               placeholder="请输入用户名" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">真实姓名</label>
    <div class="layui-input-block">
        <input type="text"  readonly  id="actual_name" name="actual_name" value="" class="layui-input"  readonly>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">余额</label>
    <div class="layui-input-block">
        <input type="text"   id="money"  name="money" value=""  class="layui-input"   readonly>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">会员类型</label>
    <div class="layui-input-block">
        <input type="text"  id="is_agent"   name="is_agent" value=""   class="layui-input" readonly>
    </div>
</div>


<div class="layui-form-item">
    <label class="layui-form-label">充值类型：</label>
    <div class="layui-input-block">
        <input type="radio" name="oreder_type" value="1" title="手动充值" checked>
        <input type="radio" name="oreder_type" value="2" title="彩金派送">
        <input type="radio" name="oreder_type" value="3" title="其他情况">
    </div>
</div>
{{--<div class="layui-form-item">
    <label for="" class="layui-form-label">充值银行</label>
    <div class="layui-input-inline" style="width: 45%;">
        <select name="bank_id" class="field-pid " type="select" lay-verify="" lay-filter="pid">
            <option value="">请选择</option>
            @foreach ($bank_list as $bank)
                <option value=" {{ $bank->id }}" @if ($bank->id === $data->bank_id) selected @endif>{{ $bank->bank_name }}</option>
            @endforeach
        </select>
    </div>
</div>--}}

<div class="layui-form-item">
    <label class="layui-form-label">充值金额</label>
    <div class="layui-input-block">
        <input type="text" name="amount" value="{{ $data->amount ?? old('amount') }}" lay-verify="required" lay-vertype="tips"
               placeholder="（扣款请在金额前加-(负号)" class="layui-input">
    </div>
</div>


<div class="layui-form-item">
    <label for="" class="layui-form-label">备注</label>
    <div class="layui-input-block">
        <textarea name="remarks" rows="7" class="layui-textarea" lay-vertype="tips"
                  placeholder="请输入备注"> {{ $data->remarks ?? old('remarks') }}</textarea>
    </div>
</div>


<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.orders')}}">返 回</a>
    </div>
</div>
