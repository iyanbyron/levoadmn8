@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.ads.store')}}" method="post" enctype="multipart/form-data">
            @include('admin.ads._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form', 'upload'], function (is_multiple) {
            var $ = layui.jquery
                , upload = layui.upload;
            var is_multiple = true;
            //多图片上传
            upload.render({
                elem: '#uploadpic'
                , url: '{{ route('admin.upload') }}' //改成您自己的上传接口
                , multiple: is_multiple
                , size: 200 //限制文件大小，单位 KB
                //,numbers: 5 //图片张数
                , before: function (obj) {
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        if ($("#ads_position").val() != 1) {
                            $("img").remove();
                        }
                        $('#showpic').append('<img src="' + result + '" alt="' + file.name + '" class="layui-upload-img" style="max-height: 100px;max-width: 100px;padding-left: 10px;">')
                    });
                }
                , done: function (res) {
                    //上传完毕
                    if (res.status == 1) { //上传成功
                        if ($("#ads_position").val() != 1) {
                            $.post("{{ route('admin.del_file') }}", {file_name: $("#old_ads_pic").val()});
                            $("#old_ads_pic").val(res.file_name)
                        }
                        $('#input_pic').append('<input type="hidden" name="ads_pic[]"  id="ads_pic" value="' + res.file_name + '">');
                    }
                }
            });
            var form = layui.form;
            form.verify({
                ads_title: [
                    /^[\S]/
                    , '标题不能为空'
                ],
                /*ads_pic: [
                    /^[\S]/
                    ,'图片不能为空'
                ]*/
            });

            form.on('select(test)', function (data) {

                var objectModel = {};
                var type = 'video_bigclass_id';
                objectModel[type] = data.value;
                $.ajax({
                    url: "{{ route('admin.ads.smallclasslist') }}", //你的路由地址
                    type: "post",
                    dataType: "json",
                    data: objectModel,
                    timeout: 30000,
                    success: function (data) {

                        $("#navigation_smallclass_id").empty();
                        var count = data['navismallclass'].length;
                        var i = 0;
                        var b = "";
                        for (i = 0; i < count; i++) {
                            b += "<option value='" + data['navismallclass'][i].id + "'>" + data['navismallclass'][i].small_name + "</option>";
                        }
                        $("#navigation_smallclass_id").append(b);
                        form.render('select'); //刷新select选择框渲染

                        $("#video_smallclass_id").empty();
                        var count = data['smallclass'].length;
                        var i = 0;
                        var b = "";
                        for (i = 0; i < count; i++) {
                            b += "<option value='" + data['smallclass'][i].id + "'>" + data['smallclass'][i].small_name + "</option>";
                        }
                        $("#video_smallclass_id").append(b);
                        form.render('select'); //刷新select选择框渲染

                    }
                });
            });

            form.on('select(ads_position)', function (data) {
                var val = data.value;
                //console.log(val);
                if (val == 1) {
                    $("#bigclass").hide();
                    $("#navismallclass").hide();
                    $("#ads_show_time").show();
                    $("#smallclass").hide();
                    is_multiple = true;
                }

                if (val == 2) {
                    $("#bigclass").show();
                    $("#navismallclass").show();
                    $("#ads_show_time").hide();
                    $("#smallclass").hide();
                    is_multiple = false;

                }
                if (val == 3) {
                    $("#bigclass").show();
                    $("#navismallclass").hide();
                    $("#ads_show_time").hide();
                    $("#smallclass").hide();
                    is_multiple = false;
                }
                if (val == 4) {
                    $("#bigclass").show();
                    $("#smallclass").show();
                    $("#navismallclass").hide();
                    $("#ads_show_time").hide();
                    is_multiple = false;
                }

                if (val == 5) {
                    $("#bigclass").hide();
                    $("#navismallclass").hide();
                    $("#ads_show_time").hide();
                    $("#smallclass").hide();
                    is_multiple = false;
                }

            });
        });
    </script>
@endsection
