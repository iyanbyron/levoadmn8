<?php

namespace App\Http\Controllers\Api;

use App\Models\Agent;
use App\Models\Order;
use App\Models\Paychannel;
use App\Models\Member;
use App\Models\AdminUser;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class NotifyController extends Controller
{
    /**
     * 微信h5异步通知上权
     * @param Request $request
     */
    public function notify(Request $request)
    {
        $paychanne_data_redis = Redis::exists("paychannel:content:WX_H5");
        if (!$paychanne_data_redis) {
            $paychanne_data = Paychannel::where('pay_type', 'WX_H5')->first();
            Redis::hmset("paychannel:content:WX_H5", $paychanne_data->toArray());
        } else {
            $paychanne_data = Redis::hgetall("paychannel:content:WX_H5");
        }
        $config = array(
            'key' => $paychanne_data['key'],
        );
        $xml = file_get_contents("php://input");
        file_put_contents(storage_path("logs/wx_h5.log"), "\r\n" . $xml . "\r\n", FILE_APPEND);
        $xml = "<xml><appid><![CDATA[wx6e7f95b4ef08ec03]]></appid>
<bank_type><![CDATA[HXB_DEBIT]]></bank_type>
<cash_fee><![CDATA[20.35]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[N]]></is_subscribe>
<mch_id><![CDATA[1528204181]]></mch_id>
<nonce_str><![CDATA[lNn9xCrGi6cvD7GFbF01tBxcu4Aj1tAE]]></nonce_str>
<openid><![CDATA[oWI_e5y_Dztk6W6-TSPyUcMGB0CI]]></openid>
<out_trade_no><![CDATA[2020062315584065484]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[F692E82D996871D75074FA6A95A6C162]]></sign>
<time_end><![CDATA[20200625155812]]></time_end>
<total_fee>2008</total_fee>
<trade_type><![CDATA[MWEB]]></trade_type>
<transaction_id><![CDATA[4200000297201905240771674456]]></transaction_id>
</xml>";
        $postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $arr = (array)$postObj;
        unset($arr['sign']);
        if (!$arr) {
            exit("无效参数");;
        }
        $order = Order::where('order_code', '=', $arr['out_trade_no'])->first();
        //验证签名
        /*if ($this->getSign($arr, $order['skey']) != $postObj->sign) {
            file_put_contents(storage_path("logs/wx_h5.log"), "\r\n" . $xml . "签名无效", FILE_APPEND);
            exit("签名无效");;
        }*/

        if ($arr) {
            //支付状态：1：未支付；2：已支付，3支付失败
            if ($order['pay_status'] == "1")//支付处理
            {
                $user_data_redis = Redis::exists("user:info:{$order['uid']}");
                if (!$user_data_redis) {
                    $user_data = Member::where('id', '=', $order['uid'])->first();
                    Redis::hmset("user:info:{$order['uid']}", $user_data->toArray());
                } else {
                    $user_data = Redis::hgetall("user:info:{$order['uid']}");
                }

                $product_data_redis = Redis::exists("product:content:{$order['product_id']}");
                if (!$product_data_redis) {
                    $product_data = Product::where('id', '=', $order['product_id'])->first();
                    Redis::hmset("product:content:{$order['product_id']}", $product_data->toArray());
                } else {
                    $product_data = Redis::hgetall("product:content:{$order['product_id']}");
                }

                if ($order['amount'] != $arr['total_fee'] / 100 || $product_data['pr_price'] != floor($arr['total_fee'] / 100)) {
                    $t1 = $arr['total_fee'] / 100;
                    file_put_contents(storage_path("logs/wx_h5.log"), "\r\n" . $xml . "金额不对", FILE_APPEND);
                    exit("金额不对");
                }
                //vip上权
                if ($order['product_type'] == '1') {
                    $in_user_data['vip_end_time'] = $this->get_vip_time($user_data['vip_end_time'], $order['days']);
                    $user_data['vip_end_time'] = $in_user_data['vip_end_time'];
                }
                //金币充值
                if ($order['product_type'] == 2) {
                    $in_user_data['balance'] = $user_data['balance'] + $order['amount'];
                    $user_data['balance'] = $in_user_data['balance'];
                    //购买金币送vip
                    if ($order['days'] > 0) $in_user_data['vip_end_time'] = $this->get_vip_time($user_data['vip_end_time'], $order['days']);
                }
                $in_order_data['pay_status'] = 2;
                $in_order_data['pay_time'] = date('Y-m-d H:i:s', strtotime($arr['time_end']));
                $in_order_data['is_deduct'] = 0;
                //更新用户金币，上权
                Member::where('id', '=', $order['uid'])
                    ->update($in_user_data);
                Redis::hmset("user:info:{$order['uid']}", $user_data);
                //统计代理费用
                $order_code = $arr['out_trade_no'];
                $agent_id = $order['admin_id'];//代理id
                $pay_amount = $order['amount'];
                $today_start = date('Y-m-d 00:00:00');
                $today_end = date('Y-m-d 23:59:59');
                $agent_paytotal_data = [];
                $agent_paytotal_data = Agent::where('created_at', '>=', $today_start)
                    ->where('created_at', '<=', $today_end)
                    ->where('admin_id', '=', $agent_id)
                    ->first();

                $agent_data_redis = Redis::exists("agent_user:content:{$agent_id}");
                if (!$agent_data_redis) {
                    $agent_data = AdminUser::where('id', $agent_id)->first();
                    Redis::hmset("agent_user:content:{$agent_id}", $agent_data->toArray());
                } else {
                    $agent_data = Redis::hgetall("agent_user:content:{$agent_id}");
                }
                //agent_total_income:扣量后代理总收入  all_total_income:所有合计总收入(未扣量)
                //total_deduct_amount:总扣除金额 total_deduct_order_num:总扣除订单数
                //total_order_num:总订单数  total_install_num:安装量
                //  total_user_num：注册用户数  agent_real_income：代理实际收入=扣量后代理总收入*分成百分比
                $agent_percent = $agent_data['agent_percent'];//代理提成百分比
                $agent_deduct_num = $agent_data['agent_deduct_num'];//代理每多少个订单扣除一个订单
                $after_days = $agent_data['after_days'];//用户注册多少天后充值不计入代理收入
                //$days会员距离当前注册了多少天
                $days = round((strtotime(date("Y-m-d H:i:s")) - strtotime($user_data['created_at'])) / 3600 / 24);
                //统计代理每天订单数量
                $order_admin_count = Order::where('admin_id', '=', $order['admin_id'])
                    ->where('pay_status', '=', 2)
                    ->where('created_at', '>=', $today_start)
                    ->where('created_at', '<=', $today_end)
                    ->count();
                /*echo $order_admin_count.'---dl---'.$agent_deduct_num;
                exit;*/
                //计入平台超级代理收入，超级代理不扣量和分成
                if ($days > $after_days) {
                    $install_arr['all_total_income'] = $agent_paytotal_data['all_total_income'] + $pay_amount;
                    $install_arr['total_order_num'] = $agent_paytotal_data['total_order_num'] + 1;
                } //计入代理收入
                else {
                    $install_arr['all_total_income'] = $agent_paytotal_data['all_total_income'] + $pay_amount;
                    $install_arr['total_order_num'] = $agent_paytotal_data['total_order_num'] + 1;
                    if (($order_admin_count + 1) % $agent_deduct_num == 0 && $order_admin_count > 0) {//扣量
                        $install_arr['total_deduct_amount'] = $agent_paytotal_data['total_deduct_amount'] + $pay_amount;
                        $install_arr['total_deduct_order_num'] = $agent_paytotal_data['total_deduct_order_num'] + 1;
                        $in_order_data['is_deduct'] = 1;
                    } else {
                        $install_arr['agent_total_income'] = $agent_paytotal_data['agent_total_income'] + $pay_amount;
                        $install_arr['agent_real_income'] = $install_arr['agent_total_income'] * $agent_percent;
                    }
                }
                //更新订单
                Order::where('order_code', '=', $arr['out_trade_no'])
                    ->update($in_order_data);
                //统计更新代理
                if ($agent_paytotal_data) {
                    $order_flag = Agent::where('id', $agent_paytotal_data['id'])->update($install_arr);
                } else {
                    $install_arr['admin_id'] = $agent_id;
                    $order_flag = Agent::create($install_arr);
                }
                echo "<xml>
            <return_code><![CDATA[SUCCESS]]></return_code>
            <return_msg><![CDATA[OK]]></return_msg>
            </xml>";
                exit;
            } else {
                echo "<xml>
            <return_code><![CDATA[SUCCESS]]></return_code>
            <return_msg><![CDATA[OK]]></return_msg>
            </xml>";
                exit;
            }

        } else {
            echo "fail";
            exit;
        }
    }

    /**
     * PAY页面跳转地址  attach: 扩展返回
     * @param Request $request
     */
    public function jumpurl(Request $request)
    {

        header('Content-type:text/html;charset=utf-8');
        $returnArray = array( // 返回字段
            "memberid" => $request->input('memberid', ''), // 商户ID
            "orderid" => $request->input('orderid', ''), // 订单号
            "amount" => $request->input('amount', ''), // 交易金额
            "datetime" => $request->input('datetime', ''), // 交易时间
            "transaction_id" => $request->input('transaction_id', ''), // 流水号
            "returncode" => $request->input('returncode', '')
        );
        $md5key = "icmm8h5puq5slkjvowqujzmtx3stizao";
        ksort($returnArray);
        reset($returnArray);
        $md5str = "";
        foreach ($returnArray as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        $sign = strtoupper(md5($md5str . "key=" . $md5key));
        if ($sign == $_REQUEST["sign"]) {
            if ($_REQUEST["returncode"] == "00") {
                $str = "交易成功！订单号：" . $_REQUEST["orderid"];
                exit($str);
            }
        }
    }


    public function argSorts($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    public function local_sign($datas = array(), $key = '')
    {
        $str = http_build_query($this->argSorts($this->paraFilters($datas)));
        $sign = md5($str . "&key=" . $key);
        return $sign;
    }

    //获取新的vip板块到期时间
    public function get_vip_time($user_end_time = '', $order_days = '')
    {
        $old_date = strtotime($user_end_time);
        if ($old_date > time()) {
            $new_time = strtotime("+{$order_days}days", $old_date);
            $end_time = date('Y-m-d H:i:s', $new_time);
        } else {
            $new_time = strtotime("+{$order_days}days", time());
            $end_time = date('Y-m-d H:i:s', $new_time);
        }
        return $end_time;
    }

    //代理结算
    function AgentAccount($order_code, $admin_id, $pay_amount, $product_type, $days)
    {
        $admin_kouliang = 4;
        $zdl_kouliang = 4;
        $dl_kouliang = 4;
        $today_start = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $today_end = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        //统计超管订单数量
        $order_admin_count = DB::table('order_recharge')
            ->where('admin_id', '=', $admin_id)
            ->where('pay_status', '=', 2)
            ->where('created_at', '>=', $today_start)
            ->where('created_at', '<=', $today_end)
            ->count();
        $order_zdl_count = $order_admin_count;
        //统计代理订单数量
        $order_dl_count = $order_admin_count;
        $arr1 = get_patientid($admin_id);
        $data['order_code'] = $order_code;
        $data['admin_id'] = $admin_id;
        $data['amount'] = $pay_amount;
        $data['product_type'] = $product_type;
        $data['days'] = $days;

        if ($admin_id == "1") {
            return AgentOrder($arr1, $data);
        }
        //总代理扣量
        if (count($arr1) == 2) {
            if ($order_admin_count % $admin_kouliang == 0) {
                $up_admin_kl = DB::table('order_recharge')
                    ->where('order_code', '=', $order_code)
                    ->update(['is_admin_kouliang' => 1]);
                $data['is_admin_kl'] = 1;//扣量
                return AgentOrder($arr1, $data);
            } else {
                $data['is_admin_kl'] = 0;////不扣量
                return AgentOrder($arr1, $data);
            }
        }
        //代理扣量
        if (count($arr1) == 3) {
            if ($order_admin_count % $admin_kouliang == 0) {
                $up_admin_kl = DB::table('order_recharge')
                    ->where('order_code', '=', $order_code)
                    ->update(['is_admin_kouliang' => 1]);
                $data['is_admin_kl'] = 1;//扣量
            } else {
                $data['is_admin_kl'] = 0;////不扣量
            }

            if ($order_zdl_count % $zdl_kouliang == 0) {
                $up_admin_kl = DB::table('order_recharge')
                    ->where('order_code', '=', $order_code)
                    ->update(['is_zdl_kouliang' => 1]);
                $data['is_zdl_kl'] = 1;//扣量
            } else {
                $data['is_zdl_kl'] = 0;////不扣量
            }
            //return $data;

            return AgentOrder($arr1, $data);
        }

        //代理用户给推广用户扣量
        if (count($arr1) == 4) {
            if ($order_admin_count % $admin_kouliang == 0) {
                $up_admin_kl = DB::table('order_recharge')
                    ->where('order_code', '=', $order_code)
                    ->update(['is_admin_kouliang' => 1]);
                $data['is_admin_kl'] = 1;//扣量
            } else {
                $data['is_admin_kl'] = 0;////不扣量
            }

            if ($order_zdl_count % $zdl_kouliang == 0) {
                $up_admin_kl = DB::table('order_recharge')
                    ->where('order_code', '=', $order_code)
                    ->update(['is_zdl_kouliang' => 1]);
                $data['is_zdl_kl'] = 1;//扣量
            } else {
                $data['is_zdl_kl'] = 0;////不扣量
            }
            if ($order_zdl_count % $zdl_kouliang == 0) {
                $up_admin_kl = DB::table('order_recharge')
                    ->where('order_code', '=', $order_code)
                    ->update(['is_zdl_kouliang' => 1]);
                $data['is_zdl_kl'] = 1;//扣量
            } else {
                $data['is_zdl_kl'] = 0;////不扣量
            }

            if ($order_dl_count % $dl_kouliang == 0) {
                $up_admin_kl = DB::table('order_recharge')
                    ->where('order_code', '=', $order_code)
                    ->update(['is_dl_kouliang' => 1]);
                $data['is_dl_kl'] = 1;//扣量
            } else {
                $data['is_dl_kl'] = 0;////不扣量
            }

            AgentOrder($arr1, $data);
        }
    }

    /**
     * 获取签名
     */
    function getSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = $this->formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }

    function formatQueryParaMap($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
}


