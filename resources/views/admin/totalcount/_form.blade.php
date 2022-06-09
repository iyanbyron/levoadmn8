{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">版本名称</label>
    <div class="layui-input-block">
        <input type="text" name="ver_name" value="{{ $data->ver_name ?? old('ver_name') }}" lay-verify="ver_name" lay-vertype="tips"
               placeholder="版本名称(如:1.0.1)" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">版本号</label>
    <div class="layui-input-block">
        <input type="text" name="ver_code" value="{{ $data->ver_code ?? old('ver_code') }}" lay-verify="required" lay-vertype="tips"
               placeholder="版本号如1,2,3,4等数字" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">下载地址</label>
    <div class="layui-input-block">
        <input type="text" name="ver_url" value="{{ $data->ver_url ?? old('ver_url') }}" lay-verify="required" lay-vertype="tips"
               placeholder="如：http://www.baidu.com" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">更新标题</label>
    <div class="layui-input-block">
        <input type="text" name="ver_title" value="{{ $data->ver_title ?? old('ver_title') }}" lay-verify="ver_title" lay-vertype="tips"
               placeholder="如：安卓1.0版本更新了" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">更新说明</label>
    <div class="layui-input-block">
        <textarea name="ver_content" rows="3" class="layui-textarea" lay-verify="required" lay-vertype="tips"
                  placeholder="请填写本次更新的内容"> {{ $data->ver_content ?? old('ver_content') }}</textarea>
    </div>
</div>


<div class="layui-row layui-col-space5">
    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">应用类型</label>
            <div class="layui-input-inline" style="width: 45%;">
                <select name="ver_app_type" class="form-control input-medium" id="position" lay-filter="position">
                    <option value="1" @if ($data->ver_app_type == 1) selected="selected" @endif>Android</option>
                    <option value="2" @if ($data->ver_app_type == 2) selected="selected" @endif>Ios</option>
                </select>
            </div>
        </div>
    </div>

    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">强制更新</label>
            <div class="layui-input-inline" style="width: 45%;">
                <input type="checkbox" name="ver_is_update" lay-skin="switch" @if ($data->ver_is_update == 1) checked @endif  lay-text="开启|关闭">
            </div>
        </div>
    </div>

</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.version')}}">返 回</a>
    </div>
</div>
