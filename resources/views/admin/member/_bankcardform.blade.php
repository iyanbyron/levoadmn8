{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">用户id</label>
    <div class="layui-input-block">
        <input type="text" name="user_id" value="{{ $data->user_id ?? old('user_id') }}"   lay-vertype="tips"
               class="layui-input"  readonly>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">用户名</label>
    <div class="layui-input-block">
        <input type="text" name="username" value="{{ $data->username ?? old('username') }}"   lay-vertype="tips"
               class="layui-input"  readonly>
    </div>
</div>

 {{--<div class="layui-form-item">
    <label for="" class="layui-form-label">银行编号</label>
    <div class="layui-input-block">
        <input type="text" name="bank_code" value="{{ $data->bank_code ?? old('bank_code') }}"   lay-vertype="tips"
               placeholder="请输入银行编号,只能数字" class="layui-input"  >
    </div>
</div>--}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">银行名称</label>
    <div class="layui-input-block">
        {{--<input type="text" name="bank_name" value="{{ $data->bank_name ?? old('bank_name') }}"   lay-vertype="tips"
               placeholder="请输入银行名称" class="layui-input"  >--}}

        <select name="bank_name" id="bank_name" lay-search>

            @foreach($bank as $banks)
                <option value="{{$banks->bank_name}}"   @if ($banks->bank_name === $data->bank_name) selected @endif>{{$banks->bank_name}}</option>
            @endforeach
        </select>

    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">支行名称</label>
    <div class="layui-input-block">
        <input type="text" name="subbranch_name" value="{{ $data->subbranch_name ?? old('subbranch_name') }}"   lay-vertype="tips"
               placeholder="请输入支行名称" class="layui-input"  >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">银行卡号</label>
    <div class="layui-input-block">
        <input type="text" name="bank_card_number" value="{{ $data->bank_card_number ?? old('bank_card_number') }}"   lay-vertype="tips"
               placeholder="请输入银行卡号,只能数字" class="layui-input"  >
    </div>
</div>


<div class="layui-form-item">
    <label for="" class="layui-form-label">开户姓名</label>
    <div class="layui-input-block">
        <input type="text" name="account_name" value="{{ $data->account_name ?? old('account_name') }}"   lay-vertype="tips"
               placeholder="请输入开户姓名" class="layui-input"  >
    </div>
</div>


<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
        <a class="layui-btn" href="{{route('admin.member')}}">返 回</a>
    </div>
</div>
