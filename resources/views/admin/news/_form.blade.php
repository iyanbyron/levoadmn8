{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">标题</label>
    <div class="layui-input-block">
        <input type="text" name="title" value="{{ $news->title ?? old('title') }}" lay-verify="required" lay-vertype="tips"
               placeholder="请输入推送标题" class="layui-input"  >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">内容</label>
    <div class="layui-input-block">
        <textarea name="content" rows="7" class="layui-textarea" lay-verify="required" lay-vertype="tips"
                  placeholder="请输入推送内容"  > {{ $news->content ?? old('content') }}</textarea>
    </div>
</div>




<div class="layui-form-item">
    <label for="" class="layui-form-label">推送对象</label>
    <div class="layui-input-block">
        <select name="news_type" id="news_type" class="field-pid" type="select" lay-filter="news_type">
            <option value="0">全部</option>
            <option value="1">会员</option>
            <option value="2">代理</option>

        </select>
    </div>
</div>


<div class="layui-form-item">
    <label for="" class="layui-form-label">用户名</label>
        <div class="layui-input-block">
            <input type="text" name="username" value="{{ $news->username ?? old('username') }}"   lay-vertype="tips"
                   placeholder="请输入用户名,推送对象为全部时不必填写" class="layui-input"  >
        </div>
</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.news')}}">返 回</a>
    </div>
</div>
