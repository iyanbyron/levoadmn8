/**
 * jquery全局函数封装
 */
(function ($) {
    /**
     * Jquery类方法
     */
    $.fn.extend({

        superForm: function (option) {
            // 默认选项
            var defaultOption = {
                buildData: function () {
                    return {};
                },
                validation: function () {
                    return true;
                },
                close: function () {
                    var index = parent.layer.getFrameIndex(window.name);
                    if (index > 0) {
                        window.parent.location.reload();
                        parent.layer.close(index);
                    }
                    location.reload();
                    return true;
                }
            };
            option = $.extend(true, {}, defaultOption, option);

            var $form = $(this)
                , btn_submit = $('.j-submit');
            $form.validator({
                onValid: function (validity) {
                    $(validity.field).next('.am-alert').hide();
                },
                /**
                 * 显示错误信息
                 * @param validity
                 */
                onInValid: function (validity) {
                    var $field = $(validity.field)
                        , $group = $field.parent()
                        , $alert = $group.find('.am-alert');

                    if ($field.data('validationMessage') !== undefined) {
                        // 使用自定义的提示信息 或 插件内置的提示信息
                        var msg = $field.data('validationMessage') || this.getValidationMessage(validity);
                        if (!$alert.length) {
                            $alert = $('<div class="am-alert am-alert-danger"></div>').hide().appendTo($group);
                        }
                        $alert.html(msg).show();
                    }
                },
                submit: function () {
                    if (this.isFormValid() === true) {
                        // 自定义验证
                        if (!option.validation())
                            return false;
                        // 禁用按钮, 防止二次提交
                        btn_submit.attr('disabled', true);
                        // 表单提交
                        $form.ajaxSubmit({
                            type: "post",
                            dataType: "json",
                            data: option.buildData(),
                            success: function (result) {
                                result.code === 1 ? (parent.layer.msg(result.msg, {
                                        time: 1200,
                                        end: function () {
                                            option.close()
                                        }
                                    }))
                                    : $.show_error(result.msg);
                                btn_submit.attr('disabled', false);
                            }
                        });
                    }
                    return false;
                }
            });
        },

        /**
         * 删除元素
         */
        delete: function (index, url, msg) {
            $(this).click(function () {
                var param = {};
                param[index] = $(this).attr('data-id');
                layer.confirm(msg ? msg : '确定要删除吗？', {title: '友情提示'}
                    , function (index) {
                        $.post(url, param, function (result) {
                            result.code === 1 ? $.show_success(result.msg, result.url)
                                : $.show_error(result.msg);
                        });
                        layer.close(index);
                    }
                );
            });
        },

        /**
         * 选择图片文件
         * @param option
         */
        selectImages: function (option) {
            var $this = this
                // 配置项
                , defaults = {
                    name: 'iFile'            // input name
                    , imagesList: '.uploader-list'    // 图片列表容器
                    , imagesItem: '.file-item'       // 图片元素容器
                    , imageDelete: '.file-item-delete'  // 删除按钮元素
                    , imageopen: '.multi-open-thumbitem'
                    , multiple: false    // 是否多选
                    , limit: null        // 图片数量 (如果存在done回调函数则无效)
                    , done: null  // 选择完成后的回调函数
                }
                , options = $.extend({}, defaults, option);
            // 显示文件库 选择文件
            $this.fileLibrary({
                type: 'image'
                , done: function (data, $touch) {
                    // 判断回调参数是否存在, 否则执行默认
                    if (typeof options.done === 'function') {
                        return options.done(data, $touch);
                    }
                    // 新增图片列表
                    var list = options.multiple ? data : [data[0]];
                    var $html = $(template('tpl-file-item', {list: list, name: options.name}))
                        , $imagesList = $this.next(options.imagesList);
                    if (
                        options.limit > 0
                        && $imagesList.find(options.imagesItem).length + list.length > options.limit
                    ) {
                        layer.msg('图片数量不能大于' + options.limit + '张', {anim: 6});
                        return false;
                    }
                    // 注册删除事件
                    $html.find(options.imageDelete).click(function () {
                        $(this).parent().remove();
                    });
                    $html.find(options.imageopen).click(function () {
                        layer.open({
                            type: 2,
                            title: false,
                            area: ['630px', '360px'],
                            shade: 0.8,
                            closeBtn: 0,
                            shadeClose: true,
                            content: $(this).find('img').attr('src')
                        });
                    })
                    // 渲染html
                    options.multiple ? $imagesList.append($html) : $imagesList.html($html);
                }
            });
        },

        selectVideo: function (option) {

            var $this = this
                // 配置项
                , defaults = {
                    name: 'iFile' // input name
                    , type: $(this).data('type')
                    , videoList: '.uploader-video-list'
                    , videoItem: '.file-item'
                    , videoDelete: '.file-item-delete'
                    , videoopen: '.multi-open-item'
                    , multiple: true    // 是否多选
                    , limit: 1        // 数量 (如果存在done回调函数则无效)
                    , done: null  // 选择完成后的回调函数
                }
                , options = $.extend({}, defaults, option);
            // 显示文件库 选择文件
            $this.fileLibrary({
                type: options.type
                , done: function (data, $touch) {
                    // 判断回调参数是否存在, 否则执行默认
                    if (typeof options.done === 'function') {
                        return options.done(data, $touch);
                    }
                    // 新增视频列表
                    var list = options.multiple ? data : [data[0]];

                    var $html = $(template('tpl-file-item', {list: list, type: options.type, name: options.name}))
                        , $videoList = $this.next(options.videoList);
                    if (
                        options.limit > 0
                        && $videoList.find(options.videoItem).length + list.length > options.limit
                    ) {
                        layer.msg('数量不能大于' + options.limit + '个', {anim: 6});
                        return false;
                    }
                    // 注册删除事件
                    $html.find(options.videoDelete).click(function () {
                        $(this).parent().remove();
                        $(options.id).show();
                    });
                    $html.find(options.videoopen).click(function () {
                        layer.open({
                            type: 2,
                            title: false,
                            area: ['630px', '360px'],
                            shade: 0.8,
                            closeBtn: 0,
                            shadeClose: true,
                            content: $(this).data('path')
                        });
                    })
                    // 渲染html
                    $(options.id).hide();
                    options.multiple ? $videoList.append($html) : $videoList.html($html);
                }
            });
        }

    });

    /**
     * Jquery全局函数
     */
    $.extend({

        /**
         * 对象转URL
         */
        urlEncode: function (data) {
            var _result = [];
            for (var key in data) {
                var value = null;
                if (data.hasOwnProperty(key)) value = data[key];
                if (value.constructor === Array) {
                    value.forEach(function (_value) {
                        _result.push(key + "=" + _value);
                    });
                } else {
                    _result.push(key + '=' + value);
                }
            }
            return _result.join('&');
        },

        /**
         * 操作成功弹框提示
         * @param msg
         * @param url
         */
        show_success: function (msg, url) {
            layer.msg(msg, {
                icon: 1
                , time: 1200
                // , anim: 1
                , shade: 0.5
                , end: function () {
                    (url !== undefined && url.length > 0) ? window.location = url : window.location.reload();
                }
            });
        },

        /**
         * 操作失败弹框提示
         * @param msg
         * @param reload
         */
        show_error: function (msg, reload) {
            var time = reload ? 1200 : 0;
            layer.alert(msg, {
                title: '提示'
                , icon: 2
                , time: time
                , anim: 6
                , end: function () {
                    reload && window.location.reload();
                }
            });
        },

        /**
         * 文件上传 (单文件)
         * 支持同一页面多个上传元素
         *  $.uploadImage({
         *   pick: '.upload-file',  // 上传按钮
         *   list: '.uploader-list' // 缩略图容器
         * });
         */
        uploadImage: function (option) {
            // 文件大小
            var maxSize = option.maxSize !== undefined ? option.maxSize : 2
                // 初始化Web Uploader
                , uploader = WebUploader.create({
                    // 选完文件后，是否自动上传。
                    auto: true,
                    // 允许重复上传
                    duplicate: true,
                    // 文件接收服务端。
                    server: STORE_URL + '/upload/image',
                    // 选择文件的按钮。可选。
                    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                    pick: {
                        id: option.pick,
                        multiple: false
                    },
                    // 文件上传域的name
                    fileVal: 'iFile',
                    // 图片上传前不进行压缩
                    compress: false,
                    // 文件总数量
                    // fileNumLimit: 1,
                    // 文件大小2m => 2097152
                    fileSingleSizeLimit: maxSize * 1024 * 1024,
                    // 只允许选择图片文件。
                    accept: {
                        title: 'Images',
                        extensions: 'gif,jpg,jpeg,bmp,png',
                        mimeTypes: 'image/*'
                    },
                    // 缩略图配置
                    thumb: {
                        quality: 100,
                        crop: false,
                        allowMagnify: false
                    },
                    // 文件上传header扩展
                    headers: {
                        'Accept': 'application/json, text/javascript, */*; q=0.01',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            //  验证大小
            uploader.on('error', function (type) {
                // console.log(type);
                if (type === "F_DUPLICATE") {
                    // console.log("请不要重复选择文件！");
                } else if (type === "F_EXCEED_SIZE") {
                    alert("文件大小不可超过" + maxSize + "m 哦！换个小点的文件吧！");
                }
            });

            // 当有文件添加进来的时候
            uploader.on('fileQueued', function (file) {
                var $uploadFile = $('#rt_' + file.source.ruid).parent()
                    , $list = $uploadFile.next(option.list)
                    , $li = $(
                        '<div id="' + file.id + '" class="file-item thumbnail">' +
                        '<img>' +
                        '<input type="hidden" name="' + $uploadFile.data('name') + '" value="">' +
                        '<i class="iconfont icon-shanchu file-item-delete"></i>' +
                        '</div>'
                    ),
                    $img = $li.find('img'),
                    $delete = $li.find('.file-item-delete');
                // 删除文件
                $delete.on('click', function () {
                    uploader.removeFile(file);
                    $delete.parent().remove();
                });
                // $list为容器jQuery实例
                $list.empty().append($li);
                // 创建缩略图
                // 如果为非图片文件，可以不用调用此方法。
                // thumbnailWidth x thumbnailHeight 为 100 x 100
                uploader.makeThumb(file, function (error, src) {
                    if (error) {
                        $img.replaceWith('<span>不能预览</span>');
                        return;
                    }
                    $img.attr('src', src);
                }, 1, 1);
            });
            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on('uploadSuccess', function (file, response) {
                if (response.code === 1) {
                    var $item = $('#' + file.id);
                    $item.addClass('upload-state-done')
                        .children('input[type=hidden]').val(response.data.path);
                } else
                    uploader.uploadError(file);
            });
            // 文件上传失败
            uploader.on('uploadError', function (file) {
                uploader.uploadError(file);
            });
            // 显示上传出错信息
            uploader.uploadError = function (file) {
                var $li = $('#' + file.id),
                    $error = $li.find('div.error');
                // 避免重复创建
                if (!$error.length) {
                    $error = $('<div class="error"></div>').appendTo($li);
                }
                $error.text('上传失败');
            };
        },

        layeropenvideo: function (option) {
            layer.open({
                type: 2,
                title: false,
                area: ['630px', '360px'],
                shade: 0.8,
                closeBtn: 0,
                shadeClose: true,
                content: option.content || ""
            });
        },

    });

})(jQuery);

/**
 * app.js
 */
$(function () {

    // 删除图片 (数据库已有的)
    $('.file-item-delete').click(function () {
        var _this = this;
        layer.confirm('您确定要删除该图片吗？', {
            title: '友情提示'
        }, function (index) {
            $(_this).parent().remove();
            $("#upload_" + $(_this).data('type')).show();
            layer.close(index);
        });
    });

    $('.multi-open-item').click(function () {
        var _this = this;
        layer.open({
            type: 2,
            title: false,
            area: ['630px', '360px'],
            shade: 0.8,
            closeBtn: 0,
            shadeClose: true,
            content: $(_this).data('path')
        });
    });

    $('.multi-open-thumbitem').click(function () {
        console.log($(this).find('img').attr('src'))
        layer.open({
            type: 2,
            title: false,
            area: ['630px', '360px'],
            shade: 0.8,
            closeBtn: 0,
            shadeClose: true,
            content: $(this).find('img').attr('src')
        });
    });

});




