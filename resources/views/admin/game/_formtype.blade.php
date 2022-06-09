{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">游戏id</label>
    <div class="layui-input-block">
        <input type="text" name="game_id" value="{{ $data->game_id ?? old('game_id') }}"   lay-vertype="tips"
                class="layui-input"  readonly>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">游戏名称</label>
    <div class="layui-input-block">
        <input type="text" name="game_name" value="{{ $data->game_name ?? old('game_name') }}"   lay-vertype="tips"
               class="layui-input"  readonly>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">和值玩法</label>
    <div class="layui-input-block">
        <input type="text" name="username" value="{{ $data->game_type ?? old('game_type') }}"   lay-vertype="tips"
               placeholder="请输入和值玩法" class="layui-input"  >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">投注限额</label>
    <div class="layui-input-block">
        <input type="text" name="username" value="{{ $data->bet_limit ?? old('bet_limit') }}"   lay-vertype="tips"
               placeholder="请输入投注限额" class="layui-input"  >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">赔率</label>
    <div class="layui-input-block">
        <input type="text" name="odds" value="{{ $data->odds ?? old('odds') }}"   lay-vertype="tips"
               placeholder="请输入赔率" class="layui-input"  >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">和值可能性</label>
    <div class="layui-input-block">
        <input type="text" name="type_v" value="{{ $data->type_v ?? old('type_v') }}"   lay-vertype="tips"
               placeholder="三个球的和值可能性,用英文逗号分开,例如:3,4,5" class="layui-input"  >
    </div>
</div>


<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.game')}}">返 回</a>
    </div>
</div>
