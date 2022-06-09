{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">推送标题</label>
    <div class="layui-input-block">
        <input type="text" name="push_title" value="{{ $data->push_title ?? old('push_title') }}" lay-verify="required" lay-vertype="tips"
               placeholder="请输入推送标题" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">推送方式</label>
    <div class="layui-input-block">
        <input type="radio" name="push_way" lay-filter="push_way" checked value="1" title="群发">
        <input type="radio" name="push_way" lay-filter="push_way" value="2" title="单发">

    </div>
</div>


<div class="layui-form-item" style="display: none" id="push_uid_div">
    <label for="" class="layui-form-label">接收用户</label>
    <div class="layui-input-block">
        <input type="text" name="push_uid" value="{{ $data->push_uid ?? old('push_uid') }}" lay-vertype="tips"
               placeholder="多个用户id,分割" class="layui-input">
    </div>
</div>


<div class="layui-form-item">
    <label class="layui-form-label">推送类型</label>
    <div class="layui-input-block">
        <select name="push_jump_type" id="push_jump_type" class="field-pid" type="select" lay-filter="push_jump_type">
            <option value="1">普通推送</option>
            <option value="2">网页推送</option>
        </select>
    </div>
</div>

<div class="layui-form-item" style="display: none" id="push_url_div">
    <label for="" class="layui-form-label">网页地址</label>
    <div class="layui-input-block">
        <input type="text" name="push_url" value="{{ $data->push_url ?? old('push_url') }}" lay-vertype="tips"
               placeholder="跳转的链接" class="layui-input">
    </div>
</div>


<div class="layui-form-item">
    <label for="" class="layui-form-label">接收设备</label>
    <div class="layui-input-block">
        <input type="radio" name="push_app_type" checked value="0" title="所有">
        <input type="radio" name="push_app_type" value="1" title="安卓">
        <input type="radio" name="push_app_type" value="2" title="苹果">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">推送内容</label>
    <div class="layui-input-block">
        <textarea name="push_content" rows="3" class="layui-textarea" lay-verify="required" lay-vertype="tips"
                  placeholder="请输入推送内容"> {{ $data->push_content ?? old('push_content') }}</textarea>
    </div>
</div>


<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.push')}}">返 回</a>
    </div>
</div>
