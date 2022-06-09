;!function (win) {

    var active = function () {

    };

    active.prototype.openLayerForm = function (url, title, data = {}, formId) {
        var formId = formId ? formId : "#layer-form", defaults = {full: false, btn: true, width: '55%', height: '65%'};
        data = $.extend({}, defaults, data);
        var d = {
            type: 2
            , title: title
            , anim: 2
            , shadeClose: true
            , content: url
            , area: [data.width, data.height]
        };
        if (data.btn) {
            d.btn = ['确认', '取消'];
            d.yes = function (index, layero) {
                var submit = layero.find('iframe').contents().find("#formDemo");
                submit.click();
            };
        }
        var index = layer.open(d);
        if (data.full) {
            layer.full(index);
        }
    };

    active.prototype.openLayerCustomForm = function (url, title, h, w, data = {}, formId) {
        var formId = formId ? formId : "#layer-form", defaults = {full: false, btn: true, width: w, height: h};
        data = $.extend({}, defaults, data);
        var d = {
            type: 2
            , title: title
            , anim: 2
            , shadeClose: true
            , content: url
            , area: [data.width, data.height]
        };
        if (data.btn) {
            d.btn = ['确认', '取消'];
            d.yes = function (index, layero) {
                var submit = layero.find('iframe').contents().find("#formDemo");
                submit.click();
            };
        }
        var index = layer.open(d);
        if (data.full) {
            layer.full(index);
        }
    };

    //新标签查看打开
    active.prototype.openLayerCustomNewtab = function (url, title, data = {}, formId) {
        if (top.layui.index) {
            top.layui.index.openTabsPage(url, title)
        } else {
            window.open(url)
        }
    };

    active.prototype.openLayerNofullForm = function (url, title, h, w, data = {}, formId) {
        var formId = formId ? formId : "#layer-form", defaults = {full: false, btn: true, width: w, height: h};
        data = $.extend({}, defaults, data);
        var d = {
            type: 2
            , title: title
            , anim: 2
            , shadeClose: true
            , content: url
            , area: [data.width, data.height]
        };
        /*if (data.btn){
            d.btn =  ['确认', '取消'];
            d.yes =  function (index, layero) {
                var submit = layero.find('iframe').contents().find("#formDemo");
                submit.click();
            };
        }*/
        var index = layer.open(d);
        if (data.full) {
            layer.full(index);
        }
    };

    active.prototype.openLayerCustom = function (url, title, h, w, data = {}, formId) {
        var formId = formId ? formId : "#layer-form", defaults = {full: false, btn: true, width: w, height: h};
        data = $.extend({}, defaults, data);
        var d = {
            type: 2
            , title: title
            , anim: 2
            , shadeClose: true
            , content: url
            , area: [data.width, data.height]
        };
        var index = layer.open(d);
        if (data.full) {
            layer.full(index);
        }
    };

    active.prototype.multi_image = function (type = 'image', multiple = '0') {
        layer.open({
            type: 2
            , shadeClose: true
            , title: '图片库'
            , area: ['50%', '80%']
            , content: "/admin/files/getFiles?type=" + type + "&multiple=" + multiple
            , btn: ['确认', '取消']
            , yes: function (index, layero) {
                var index = parent.layer.getFrameIndex(window.name);
                var submit = layero.find('iframe').contents().find("#submit");
                submit.click();
                layer.close(index);
            }
        })
    };
    active.prototype.deleteMultiImage = function (elm, t = 0) {
        if (t == 1) {
            $("input[name=" + $(elm).closest(".layui-upload").data('name') + "]").val('');
            $(elm).parent().remove();
        } else {
            $(elm).parent().remove();
        }

    };


    win.active = new active();
}(window);
