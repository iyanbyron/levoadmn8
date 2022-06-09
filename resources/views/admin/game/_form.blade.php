{{csrf_field()}}

<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">游戏名称：</label>
            <div class="layui-input-block">
                <input type="text"  name="game_name"   value="{{ $data->game_name ?? old('game_name') }}" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">游戏状态：</label>
            <div class="layui-input-block">
                <input type="radio" name="game_status" value="1" title="开启" @if ($data->game_status == 1) checked @endif>
                <input type="radio" name="game_status" value="0" title="关闭" @if ($data->game_status == 0) checked @endif>
            </div>
        </div>
    </div>
</div>


<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">当天开始时间：</label>
            <div class="layui-input-block">
                <input type="text"   name="start_time"   value="{{ $data->start_time ?? old('start_time') }}" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">当天结束时间：</label>
            <div class="layui-input-block">
                <input type="text"   name="end_time"  value="{{ $data->end_time ?? old('end_time') }}" class="layui-input">
            </div>
        </div>
    </div>
</div>


<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">彩种限红：</label>
            <div class="layui-input-block">
                <input type="text"  name="color_limited_red"  value="{{ $data->color_limited_red ?? old('color_limited_red') }}" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">单注限红：</label>
            <div class="layui-input-block">
                <input type="text"    name="single_bet_limit_red"   value="{{ $data->single_bet_limit_red ?? old('single_bet_limit_red') }}" class="layui-input">
            </div>
        </div>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">排序：</label>
            <div class="layui-input-block">
                <input type="text"  name="sort"  value="{{ $data->sort ?? old('sort') }}" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">赔率：</label>
            <div class="layui-input-block">
                <input type="text"    name="odds"   value="{{ $data->odds ?? old('odds') }}" class="layui-input">
            </div>
        </div>
    </div>
</div>


<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">开奖间隔时间：</label>
            <div class="layui-input-block">
                <input type="text"  name="time_interval"  value="{{ $data->time_interval ?? old('time_interval') }}" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">最小投注额度：</label>
            <div class="layui-input-block">
                <input type="text"    name="min_bet_money"   value="{{ $data->min_bet_money ?? old('min_bet_money') }}" class="layui-input">
            </div>
        </div>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">游戏简述：</label>
            <div class="layui-input-block">
                <input type="text"  name="game_remark"  value="{{ $data->game_remark ?? old('game_remark') }}" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">游戏元素：</label>
            <div class="layui-input-block">
                <input type="text"    name="game_element"   value="{{ $data->game_element ?? old('game_element') }}" class="layui-input">
            </div>
        </div>
    </div>
</div>



<div class="layui-form-item">
    <div class="layui-col-lg6">
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">前台玩法菜单：</label>
            <div class="layui-input-block">
                <input type="text"  name="front_play_menu"  value="{{ $data->front_play_menu ?? old('front_play_menu') }}" class="layui-input">
            </div>
        </div>
        <div class="layui-col-lg3" style="width: 50%; float: left;">
            <label class="layui-form-label">单局分盘时间：</label>
            <div class="layui-input-block">
                <input type="text"    name="split_time"   value="{{ $data->split_time ?? old('split_time') }}" class="layui-input">
            </div>
        </div>
    </div>
</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.game')}}">返 回</a>
    </div>
</div>
