{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" lay-vertype="tips" value="{{$role->name??old('name')}}" placeholder="如:admin">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">显示名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" lay-vertype="tips" value="{{$role->display_name??old('display_name')}}" placeholder="如：管理员">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block layui-hide">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a href="{{route('admin.role')}}" class="layui-btn">返 回</a>
    </div>
</div>
