{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">所属大类</label>
    <div class="layui-input-inline" style="width: 45%;">
        <select name="video_bigclass_id" class="field-pid " type="select" lay-verify="" lay-filter="pid">
            <option value="">请选择</option>
            @foreach ($bigclass_list as $bigclass)
                <option value=" {{ $bigclass->id }}" @if ($bigclass->id === $data->video_bigclass_id) selected @endif>{{ $bigclass->big_name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input type="text" name="label_name" value="{{ $data->label_name ?? old('label_name') }}" lay-verify="required" lay-vertype="tips"
               placeholder="请输入标签名称" class="layui-input">
    </div>
</div>


<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.label')}}">返 回</a>
    </div>
</div>
