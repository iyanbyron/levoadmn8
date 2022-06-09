@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.member.update',$user->id)}}" method="post">
            <input type="hidden" name="id" value="{{$user->id}}">
            {{method_field('put')}}
            @include('admin.member._viewform')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                /*uid: [
                    /^[\S]{4,14}$/
                    ,'用户名必须至少4到14字符'
                ]*/
            });
        });
    </script>
@endsection
