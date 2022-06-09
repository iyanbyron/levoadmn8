<?php
$url = $_REQUEST['url'];
$package = $_REQUEST['package'];
$redirect_url = urlencode($_REQUEST['redirect_url']);
//url=https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id=wx26223649027954409170b2832550520000
//&package=829919446
//&redirect_url=http://www.hansenjixiemuju.com/wxpay/success.php
$url_post = $url . '&package=' . $package . '&redirect_url=' . $redirect_url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>支付加载中</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no"/>
    <style type="text/css">
        body {
            text-align: center;
        }

        .sihu {
            color: #0066CC;
            margin: 0 auto;
            width: 300px;
            height: 60px;
            margin-top: 200px;
        }
    </style>
</head>
<body>

<div class="sihu">
    <h2>
        <form action="<?php echo $url_post; ?>" method="post" οnsubmit="return validate(document.getElementByIdx_x('code_input'));">
            <input type="hidden" name="SH_WX" value="1">
            请输入验证码后确定支付：<br>
            <div id="v_container" style="width: 280px;height: 38px;"></div>
            <input type="text" id="code_input" value="" placeholder="请输入验证码"/>
            <button id="my_button">确定支付</button>
        </form>
    </h2>
</div>
<script>
    var pFocus = document.getElementById("code_input");
    pFocus.focus();
    pFocus.select();
    !(function (window, document) {
        function GVerify(options) { //创建一个图形验证码对象，接收options对象为参数
            this.options = { //默认options参数值
                id: "", //容器Id
                canvasId: "verifyCanvas", //canvas的ID
                width: "100", //默认canvas宽度
                height: "30", //默认canvas高度
                type: "blend", //图形验证码默认类型blend:数字字母混合类型、number:纯数字、letter:纯字母
                code: ""
            }

            if (Object.prototype.toString.call(options) == "[object Object]") {//判断传入参数类型
                for (var i in options) { //根据传入的参数，修改默认参数值
                    this.options[i] = options[i];
                }
            } else {
                this.options.id = options;
            }

            this.options.numArr = "0,1,2,3,4,5,6,7,8,9".split(",");
            this.options.letterArr = getAllLetter();

            this._init();
            this.refresh();
        }

        GVerify.prototype = {
            /**版本号**/
            version: '1.0.0',

            /**初始化方法**/
            _init: function () {
                var con = document.getElementById(this.options.id);
                var canvas = document.createElement("canvas");
                this.options.width = con.offsetWidth > 0 ? con.offsetWidth : "100";
                this.options.height = con.offsetHeight > 0 ? con.offsetHeight : "30";
                canvas.id = this.options.canvasId;
                canvas.width = this.options.width;
                canvas.height = this.options.height;
                canvas.style.cursor = "pointer";
                canvas.innerHTML = "您的浏览器版本不支持canvas";
                con.appendChild(canvas);
                var parent = this;
                canvas.onclick = function () {
                    parent.refresh();
                }
            },

            /**生成验证码**/
            refresh: function () {
                this.options.code = "";
                var canvas = document.getElementById(this.options.canvasId);
                if (canvas.getContext) {
                    var ctx = canvas.getContext('2d');
                } else {
                    return;
                }

                ctx.textBaseline = "middle";

                ctx.fillStyle = randomColor(180, 240);
                ctx.fillRect(0, 0, this.options.width, this.options.height);

                if (this.options.type == "blend") { //判断验证码类型
                    var txtArr = this.options.numArr.concat(this.options.letterArr);
                } else if (this.options.type == "number") {
                    var txtArr = this.options.numArr;
                } else {
                    var txtArr = this.options.letterArr;
                }

                for (var i = 1; i <= 4; i++) {
                    var txt = txtArr[randomNum(0, txtArr.length)];
                    this.options.code += txt;
                    ctx.font = randomNum(this.options.height / 2, this.options.height) + 'px SimHei'; //随机生成字体大小
                    ctx.fillStyle = randomColor(50, 160); //随机生成字体颜色
                    ctx.shadowOffsetX = randomNum(-3, 3);
                    ctx.shadowOffsetY = randomNum(-3, 3);
                    ctx.shadowBlur = randomNum(-3, 3);
                    ctx.shadowColor = "rgba(0, 0, 0, 0.3)";
                    var x = this.options.width / 5 * i;
                    var y = this.options.height / 2;
                    var deg = randomNum(-30, 30);
                    /**设置旋转角度和坐标原点**/
                    ctx.translate(x, y);
                    ctx.rotate(deg * Math.PI / 180);
                    ctx.fillText(txt, 0, 0);
                    /**恢复旋转角度和坐标原点**/
                    ctx.rotate(-deg * Math.PI / 180);
                    ctx.translate(-x, -y);
                }
                /**绘制干扰线**/
                //for(var i = 0; i < 4; i++) {
                for (var i = 0; i < 0; i++) {
                    ctx.strokeStyle = randomColor(40, 180);
                    ctx.beginPath();
                    ctx.moveTo(randomNum(0, this.options.width), randomNum(0, this.options.height));
                    ctx.lineTo(randomNum(0, this.options.width), randomNum(0, this.options.height));
                    ctx.stroke();
                }
                /**绘制干扰点**/
                //for(var i = 0; i < this.options.width/4; i++) {
                for (var i = 0; i < 0; i++) {
                    ctx.fillStyle = randomColor(0, 255);
                    ctx.beginPath();
                    ctx.arc(randomNum(0, this.options.width), randomNum(0, this.options.height), 1, 0, 2 * Math.PI);
                    ctx.fill();
                }
            },

            /**验证验证码**/
            validate: function (code) {
                var code = code.toLowerCase();
                var v_code = this.options.code.toLowerCase();
                console.log(v_code);
                if (code == v_code) {
                    return true;
                } else {
                    this.refresh();
                    return false;
                }
            }
        }

        /**生成字母数组**/
        function getAllLetter() {
            var letterStr = "0,1,2,3,4,5,6,7,8,9";
            return letterStr.split(",");
        }

        /**生成一个随机数**/
        function randomNum(min, max) {
            return Math.floor(Math.random() * (max - min) + min);
        }

        /**生成一个随机色**/
        function randomColor(min, max) {
            var r = randomNum(min, max);
            var g = randomNum(min, max);
            var b = randomNum(min, max);
            return "rgb(" + r + "," + g + "," + b + ")";
        }

        window.GVerify = GVerify;
    })(window, document);

</script>
<script>
    var verifyCode = new GVerify("v_container");

    document.getElementById("my_button").onclick = function () {
        var res = verifyCode.validate(document.getElementById("code_input").value);
        if (res) {
            //alert("验证正确");
            /*if (confirm("提交表单?")) {
                alert(obj.value);
                return true;
            } else {
                alert(obj.value);
                return false;
            }*/
            return true;
        } else {
            alert("验证码错误");
            return false;
        }
    }
</script>
</body>
</html>
