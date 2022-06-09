@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.videosmallclass.update',$data->id)}}" method="post">
            <input type="hidden" name="id" value="{{$data->id}}">
            {{method_field('put')}}
            @include('admin.videosmallclass._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                small_name: [
                    /^[\S]/
                    , '小类名称不能为空'
                ],
                small_introduction: [
                    /^[\S]/
                    , '小类简介不能为空'
                ]
            });
        });
    </script>
@endsection
