<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SetRedisController;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Paychannel;
use App\Models\Order;
use App\Http\Controllers\Api\WechatH5PayController;
use App\Models\Site;
use Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;


class PayController extends Controller
{
    /**
     * 定义jwt验证$guard参数
     * @var string
     */
    protected $guard = 'api';

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getuser']]);
    }

    /**
     * 支付渠道：pay_type:1:WX_H5
     *  pr_id:产品id  pr_type：产品类型 1：vip 2:金币   agent_id：代理id,3默认
     * */
    public function ToPay(Request $request)
    {
        /*file_put_contents(storage_path("logs/wx_h5.log") , "\r\n" . '11111sadsad' . "\r\n", FILE_APPEND);
        exit;*/
        $pr_id = $request->pr_id;
        $pay_type = $request->pay_type;
        $agent_id = !empty($request->agent_id) ? $request->agent_id : 3;
        if ($pr_id == "" || $pay_type == "") {
            return $this->fail('参数不能为空！', "400");
        }
        //支付渠道数据
        $paychanne_data_redis = Redis::exists("paychannel:content:{$pay_type}");
        if (!$paychanne_data_redis) {
            $paychanne_data = Paychannel::where('pay_type', $pay_type)->first();
            Redis::hmset("paychannel:content:" . $pay_type, $paychanne_data->toArray());
        } else {
            $paychanne_data = Redis::hgetall("paychannel:content:{$pay_type}");
        }
        //产品数据
        $product_data_redis = Redis::exists("product:content:{$pr_id}");
        if (!$product_data_redis) {
            $product_data = Product::where('id', $pr_id)->first();
            Redis::sadd("product:type_{$product_data['pr_type']}", $product_data['id']);
            Redis::hmset("product:content:" . $product_data['id'], $product_data->toArray());
        } else {
            $product_data = Redis::hgetall("product:content:{$pr_id}");
        }

        if (empty($paychanne_data) || empty($product_data)) {
            return $this->fail('产品或支付渠道不存在！', "400");
        }
        //产品类型：1vip充值，2金币币充值,3金币购买vip
        $product_type = $product_data['pr_type'];
        $payment_method = $paychanne_data['pay_type'];
        $amount = $product_data['pr_price'];
        if ($paychanne_data['pay_is_rend'] == '1') {
            $rend_amount = round(lcg_value() . PHP_EOL, 2);
            if ($rend_amount > 0.39) $rend_amount = round($rend_amount - 0.39, 2);
            $amount = $amount + $rend_amount;
        }
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user['user_id'];
        $uid = $user['id'];
        $token = Auth::guard($this->guard)->getToken()->get();
        //生成订单号
        mt_srand((double )microtime() * 1000000);
        $order_id = date("YmdHis") . str_pad(mt_rand(1, 99999), 5, "0", 0);
        $pay_pname_data = explode(',', $paychanne_data['pay_prname']);
        $product_name = array_rand($pay_pname_data, 1);
        $sh_config = [
            'mch_id' => $paychanne_data['mch_id'],
            'appid' => $paychanne_data['appid'],
            'key' => $paychanne_data['key'],
            'notify_url' => $paychanne_data['notify_url'],//通知地址
            'redirect_url' => $paychanne_data['redirect_url'],//跳转前端地址
            'openid' => "",
            'package' => "www.163vip." . $paychanne_data['id'],
        ];

        $sign_data = $this->wx_sign_data($product_name, $amount * 100, $pay_type, $order_id, $sh_config);
        $wechatAppPay = new WechatH5PayController($sh_config ['appid'], $sh_config ['mch_id'], $sh_config ['notify_url'], $sh_config ['key']);//实例化微信支付类对象
        $sign = $wechatAppPay->MakeSign($sign_data);//生成支付签名//生成支付签名
        $result = $wechatAppPay->unifiedOrder($sign_data); // result中就是返回的各种信息
        if ($result['return_code'] == 'FAIL') {
            return $this->fail('微信H5支付:' . $result['return_msg'], "400");
        }
        //生成订单
        $insert_order_data = [
            'order_code' => $order_id,
            'uid' => $uid,
            'amount' => $amount,
            'payment_method' => $payment_method,
            'days' => $product_data['pr_days'],
            'gold_num' => $product_data['pr_gold_num'],
            'product_type' => $product_data['pr_type'],
            'product_id' => $product_data['id'],
            'admin_id' => $agent_id,
        ];
        $order_id_arr = Order::create($insert_order_data);
        $url = $sh_config['redirect_url'];
        $html_to_url = $result['mweb_url'] . "&redirect_url=" . $url;
        $scheme = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
        $html_url = $scheme . $_SERVER['HTTP_HOST'] . "/pay/wxpay_h5.php?url=" . $html_to_url;
        $data['pay_url'] = $html_url;
        $data['order'] = $order_id_arr;
        //file_put_contents($pay_path, $html);
        return $this->success($data, 'Bearer ' . $token);//$order_id_arr


    }

    //微信签名参数
    function wx_sign_data($body, $amount, $pay_type, $order_id, $sh_config)
    {
        $config = $sh_config;
        $data = array(
            "appid" => $config['appid'],
            "mch_id" => $config['mch_id'],
            "nonce_str" => $this->getNonceNum(),
            "body" => $body,//产品名称
            "out_trade_no" => $order_id,
            "total_fee" => $amount,
            "spbill_create_ip" => $this->getIp(),
            "notify_url" => $config['notify_url'],
            "trade_type" => "MWEB", //H5 支付类型,
            // "package" => $config['package'],
        );
        return $data;
    }

    //生成随机字符串
    function getNonceNum($numLen = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $numLen; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    //获取用户IP地址
    public function getIp()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }
}

