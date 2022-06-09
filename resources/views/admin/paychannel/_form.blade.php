{{csrf_field()}}
<div class="layui-row layui-col-space5">
    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">渠道名称</label>
            <div class="layui-input-block">
                <input type="text" name="chann_title" value="{{ $data->chann_title ?? old('chann_title') }}" lay-verify="required" lay-vertype="tips"
                       placeholder="如:官方微信支付" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">支付类型</label>
            <div class="layui-input-block">
                <input type="text" name="pay_type" value="{{ $data->pay_type ?? old('pay_type') }}" lay-verify="required" lay-vertype="tips"
                       placeholder="如:LEVO_WX" class="layui-input">
            </div>
        </div>
    </div>

</div>


<div class="layui-row layui-col-space5">
    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">支付名称</label>
            <div class="layui-input-block">
                <input type="text" name="pay_title" value="{{ $data->pay_title ?? old('pay_title') }}" lay-verify="required" lay-vertype="tips"
                       placeholder="请输入支付名称" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">商户号</label>
            <div class="layui-input-block">
                <input type="text" name="mch_id" value="{{ $data->mch_id ?? old('mch_id') }}" lay-verify="required" lay-vertype="tips"
                       placeholder="如:1562659081" class="layui-input">
            </div>
        </div>
    </div>
</div>

<div class="layui-row layui-col-space5">
    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">appid</label>
            <div class="layui-input-block">
                <input type="text" name="appid" value="{{ $data->appid ?? old('appid') }}" lay-vertype="tips"
                       placeholder="如:wx7433e3bc6a85e5b2" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">秘钥</label>
            <div class="layui-input-block">
                <input type="text" name="key" value="{{ $data->key ?? old('key') }}" lay-verify="required" lay-vertype="tips"
                       placeholder="如:sdhadguasjdbnkjgfdesadASjshXT13V" class="layui-input">
            </div>
        </div>
    </div>
</div>


<div class="layui-row layui-col-space5">
    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">通知地址</label>
            <div class="layui-input-block">
                <input type="text" name="notify_url" value="{{ $data->notify_url ?? old('notify_url') }}" lay-verify="required" lay-vertype="tips"
                       placeholder="以http://或https://开头" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">跳转地址</label>
            <div class="layui-input-block">
                <input type="text" name="redirect_url" value="{{ $data->redirect_url ?? old('redirect_url') }}" lay-vertype="tips"
                       placeholder="以http://或https://开头" class="layui-input">
            </div>
        </div>
    </div>
</div>


<div class="layui-row layui-col-space5">
    <div class="layui-col-md4">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">提交地址</label>
            <div class="layui-input-block">
                <input type="text" name="submit_url" value="{{ $data->submit_url ?? old('submit_url') }}" lay-verify="required" lay-vertype="tips"
                       placeholder="以http://或https://开头" class="layui-input">
            </div>
        </div>
    </div>

    <div class="layui-col-md4">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">小数金额</label>
            <div class="layui-input-block">
                <input type="radio" name="pay_is_rend" value="1"
                       @if ($data->pay_is_rend=== 1)  checked @endif  title="开">
                <input type="radio" name="pay_is_rend" value="0" title="关"
                       @if ($data->pay_is_rend=== 0)  checked @endif>
            </div>
        </div>
    </div>

    <div class="layui-col-md4">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="is_open" value="1"
                       @if ($data->is_open=== 1)  checked @endif  title="开">
                <input type="radio" name="is_open" value="0" title="关"
                       @if ($data->is_open=== 0)  checked @endif>
            </div>
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">产品名称</label>
    <div class="layui-input-block">
        <textarea name="pay_prname" lay-verify="required" placeholder="如：LOGO电子门禁卡片x5,LOGO浴巾,LOGO厕所防雾镜,LOGO杯具烟缸套" class="layui-textarea">{{ $data->pay_prname ?? old('pay_prname') }}</textarea>
    </div>
</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.product')}}">返 回</a>
    </div>
</div>
