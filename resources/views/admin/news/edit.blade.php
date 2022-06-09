@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.news.update',$news->id)}}" method="post">
            <input type="hidden" name="id" value="{{$news->id}}">
            {{method_field('put')}}
            @include('admin.news._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                title: [
                    /^[\S]/
                    , '标题不能为空'
                ],
                content: [
                    /^[\S]/
                    , '内容不能为空'
                ]
            });
        });
    </script>
@endsection
