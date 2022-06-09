@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.paychannel.store')}}" method="post">
            @include('admin.paychannel._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;
            form.verify({
                pay_title: [
                    /^[\S]/
                    , '标题不能为空'
                ],
                mch_id: [
                    /^[\S]/
                    , '商户号不能为空'
                ]
            });
        });
    </script>
@endsection
