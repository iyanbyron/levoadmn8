@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15" style="background-color: #fff;">

        <div class="layui-col-sm6">
            <fieldset class="layui-elem-field ">
                <legend><a name="default">环境</a></legend>
                <div class="layui-field-box">
                    <table class="layui-table" lay-skin="line">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($envs as $env)
                            <tr>
                                <td>{{ $env['name'] }}</td>
                                <td>{{ $env['value'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>

        <div class="layui-col-sm6">
            <fieldset class="layui-elem-field ">
                <legend><a name="default">依赖</a></legend>
                <div class="layui-field-box">

                    <table class="layui-table" lay-skin="line">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dependencies as $key => $val)
                            <tr>
                                <td>{{$key}}</td>
                                <td><span class="layui-btn layui-bg-blue layui-btn-xs">{{$val}}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>

    </div>
@endsection

