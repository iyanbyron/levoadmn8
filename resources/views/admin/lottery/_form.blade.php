{{csrf_field()}}

    {{--<div class="layui-form-item">
        <label for="" class="layui-form-label">商品标题</label>
        <div class="layui-input-block">
            <input type="text" name="pr_title" value="{{ $data->pr_title ?? old('pr_title') }}" lay-verify="required" lay-vertype="tips"
                   placeholder="请输入商品标题" class="layui-input">
        </div>
    </div>--}}

    <div class="layui-form-item">
        <label class="layui-form-label">期号</label>
        <div class="layui-input-block" >
            <input type="text"  readonly   value="{{ $data->issue ?? old('issue') }}" class="layui-input">
        </div>
    </div>


    <div class="layui-form-item">
        <label for="" class="layui-form-label">日期</label>
        <div class="layui-input-block">
            <input type="text"    readonly    value="{{ $data->open_time ?? old('open_time') }}"  class="layui-input">
        </div>
    </div>


<div class="layui-form-item">
    <label for="" class="layui-form-label">开奖号码</label>
    <div class="layui-input-block">
        <input type="text"     name="win_number" value="{{ $data->win_number ?? old('win_number') }}"  class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">开奖状态：</label>
    <div class="layui-input-block">
        <input type="radio" name="is_open" value="1" title="立即开奖" @if ($data->is_open == 1) checked @endif>
        <input type="radio" name="is_open" value="0" title="预留开奖号" @if ($data->is_open == 0) checked @endif>
    </div>
</div>


    <div class="layui-form-item layui-hide">
        <div class="layui-input-block">
            <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
            <a class="layui-btn" href="{{route('admin.lottery')}}">返 回</a>
        </div>
    </div>

