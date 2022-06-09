<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SetRedisController;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Share;
use App\Models\Site;
use Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;


class ShareController extends Controller
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
     * 分享
     *
     * */
    public function ShareList(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $token = Auth::guard($this->guard)->getToken()->get();
        $uid = $user['id'];
        $domain = unserialize(Redis::get("system:sites"));
        if (!$domain) {
            $domain_data = Site::first();
            $domain = json_decode($domain_data['value'], true);
            Redis::set("system:sites", serialize($domain));
        }
        $share_info_redis = Redis::exists("share:info:{$uid}");
        if (!$share_info_redis) {
            $share_info = Share::where('uid', '=', "{$uid}")->first();
            $share_info ? $share_info = $share_info->toArray() : $share_info = [];
            if (empty($share_info)) {
                $invite_code = $this->make_invite_code();
                $data = [
                    'uid' => $uid,
                    'invi_code' => $invite_code,
                    'invi_uid' => 0,
                    'invi_num' => 0,
                ];
                Share::create($data);
                $data['share_title'] = $domain['share_title'] . $invite_code . '    ' . $domain['share_domain'] . "?code={$invite_code}";
                Redis::hmset("share:info:{$uid}", $data);
                Redis::expire("share:info:{$uid}", 60 * 60 * 24 * 30);
                Redis::hmset("share:info:{$invite_code}", $data);
                Redis::expire("share:info:{$invite_code}", 60 * 60 * 24 * 30);
                $share_info = $data;
            } else {
                $invite_code = $share_info['invi_code'];
                Redis::hmset("share:info:{$uid}", $share_info);
                Redis::expire("share:info:{$uid}", 60 * 60 * 24 * 30);
                Redis::hmset("share:info:{$invite_code}", $share_info);
                Redis::expire("share:info:{$invite_code}", 60 * 60 * 24 * 30);
            }
        } else {
            $share_info = Redis::hgetall("share:info:{$uid}");
        }
        if (!empty($share_info)) {
            $share_info['invite_task'] = [
                'task_vip' => "-填写推荐码 vip天数+{$domain['share_vip_days']}",
                'task_gold' => "-填写推荐码 金币+{$domain['share_gold_num']}",
                'invite_vip' => "-每推广1人 vip天数+{$domain['share_vip_days']}",
                'invite_gold' => "-每推广1人 金币+{$domain['share_gold_num']}",
            ];
            return $this->success($share_info, $token, '成功');
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    /**
     * 填写邀请码
     *
     * */
    public function SendInviteCode(Request $request)
    {
        $invite_code = $request->invite_code;
        $user = JWTAuth::parseToken()->authenticate();
        $token = Auth::guard($this->guard)->getToken()->get();
        $uid = $user['id'];
        //邀请人
        $share_yq_redis = Redis::exists("share:info:{$invite_code}");
        if (!$share_yq_redis) {
            $share_yq_info = Share::where('invi_code', '=', "{$invite_code}")->first();
            $share_yq_info ? $share_yq_info = $share_yq_info->toArray() : $share_yq_info = [];
            if (empty($share_info)) {
                return $this->fail('邀请码不存在！', "400");
            }
        } else {
            $share_yq_info = Redis::hgetall("share:info:{$invite_code}");//邀请人
        }
        if ($share_yq_info['invi_uid'] > 0) {
            return $this->fail('此用户已被邀请！', "400");
        }
        //被邀请人
        $share_info = Share::where('uid', '=', "{$uid}")->first();
        if ($share_yq_info['uid'] == $share_info['invi_uid']) {
            return $this->fail('该邀请码用户已填写过你的推荐码！', "400");
        }
        $domain = unserialize(Redis::get("system:sites"));
        if (!$domain) {
            $domain_data = Site::first();
            $domain = json_decode($domain_data['value'], true);
            Redis::set("system:sites", serialize($domain));
        }

        $share_info ? $share_info = $share_info->toArray() : $share_info = [];
        if (empty($share_info)) {
            $invite_code = $this->make_invite_code();
            $domain = unserialize(Redis::get("system:sites"));
            if (!$domain) {
                $domain_data = Site::first();
                $domain = json_decode($domain_data['value'], true);
                Redis::set("system:sites", serialize($domain));
            }
            $data = [
                'uid' => $uid,
                'invi_code' => $invite_code,
                'invi_uid' => $share_yq_info['uid'],
                'invi_num' => 0,
            ];
            Share::create($data);
            $data['share_title'] = $domain['share_title'] . $invite_code . '    ' . $domain['share_domain'] . "?code={$invite_code}";
            Share::where('uid', $share_yq_info['uid'])->increment('invi_num', 1);
            Redis::hIncrBy("share:info:{$share_yq_info['uid']}", 'invi_num', 1);
            Redis::hIncrBy("share:info:{$share_yq_info['invi_code']}", 'invi_num', 1);

            Redis::hmset("share:info:{$uid}", $data);
            Redis::expire("share:info:{$uid}", 60 * 60 * 24 * 30);
            Redis::hmset("share:info:{$invite_code}", $data);
            Redis::expire("share:info:{$invite_code}", 60 * 60 * 24 * 30);
            $share_info = $data;
        } else {
            //是否已经填写过此邀请码
            $is_share_data = Share::where('uid', '=', "{$uid}")
                ->where('invi_uid', '=', "{$share_yq_info['uid']}")->first();
            if (empty($is_share_data)) {
                Share::where('uid', $uid)->update(['invi_uid' => $share_yq_info['uid']]);
                Share::where('uid', $share_yq_info['uid'])->increment('invi_num', 1);
                Redis::hIncrBy("share:info:{$share_yq_info['uid']}", 'invi_num', 1);
                Redis::hIncrBy("share:info:{$share_yq_info['invi_code']}", 'invi_num', 1);
                $invite_code = $share_info['invi_code'];
                Redis::HSET("share:info:{$uid}", 'invi_uid', $share_yq_info['uid']);
                Redis::expire("share:info:{$uid}", 60 * 60 * 24 * 30);
                Redis::HSET("share:info:{$invite_code}", 'invi_uid', $share_yq_info['uid']);
                Redis::expire("share:info:{$invite_code}", 60 * 60 * 24 * 30);
            } else {
                return $this->fail('不能重复填写邀请码！', "400");
            }
        }
        if (!empty($share_info)) {
            //赠送天数，金币，
            $give_days = $domain['share_vip_days'];
            $give_gold = $domain['share_gold_num'];
            $data = [];
            //邀请人赠送vip，金币
            $user_info_redis = Redis::exists("user:info:{$share_yq_info['uid']}");
            if (!$user_info_redis) {
                $yq_user = Member::findOrFail($share_yq_info['uid']);
            } else {
                $yq_user = Redis::hgetall("user:info:{$share_yq_info['uid']}");
            }
            $old_date = strtotime($yq_user['vip_end_time']);
            if ($old_date > time()) {
                $new_time = strtotime("+{$give_days}days", $old_date);
                $end_time = date('Y-m-d H:i:s', $new_time);
            } else {
                $new_time = strtotime("+{$give_days}days", time());
                $end_time = date('Y-m-d H:i:s', $new_time);
            }
            $data['vip_end_time'] = $end_time;
            $data['balance'] = $yq_user['balance'] + $give_gold;
            if (Member::where('id', $share_yq_info['uid'])->update($data)) {
                //更新Redis缓存
                $data['vip_is_end'] = 0;
                $data['is_vip'] = 1;
                Redis::hmset("user:info:{$yq_user['user_id']}", $data);
                Redis::hmset("user:info:{$yq_user['id']}", $data);
                Redis::expire("user:info:{$yq_user['user_id']}", 60 * 60 * 24 * 1);
                Redis::expire("user:info:{$yq_user['id']}", 60 * 60 * 24 * 1);
            }
            //被邀请人赠送vip，金币
            $data = [];
            $user_info_redis = Redis::exists("user:info:{$share_info['uid']}");
            if (!$user_info_redis) {
                $user = Member::findOrFail($share_info['uid']);
            } else {
                $user = Redis::hgetall("user:info:{$share_info['uid']}");
            }
            $old_date = strtotime($user['vip_end_time']);
            if ($old_date > time()) {
                $new_time = strtotime("+{$give_days}days", $old_date);
                $end_time = date('Y-m-d H:i:s', $new_time);
            } else {
                $new_time = strtotime("+{$give_days}days", time());
                $end_time = date('Y-m-d H:i:s', $new_time);
            }
            $data['vip_end_time'] = $end_time;
            $data['balance'] = $user['balance'] + $give_gold;
            if (Member::where('id', $share_info['uid'])->update($data)) {
                //更新Redis缓存
                $data['vip_is_end'] = 0;
                $data['is_vip'] = 1;
                Redis::hmset("user:info:{$user['user_id']}", $data);
                Redis::hmset("user:info:{$user['id']}", $data);
                Redis::expire("user:info:{$user['user_id']}", 60 * 60 * 24 * 1);
                Redis::expire("user:info:{$user['id']}", 60 * 60 * 24 * 1);
            }
            return $this->success($share_info, $token, '成功');
        } else {
            return $this->fail('操作失败！', "400");
        }
    }


    /**生成单个邀请码
     *      * @return string
     *      */
    public static function make_invite_code()
    {
        $code = "ABCDEFGHIGKLMNOPQRSTUVWXYZ";
        $rand = $code[rand(0, 25)] . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            $d = '',
            $f = 0;
            $f < 6;
            $g = ord($a[$f]), // ord（）函数获取首字母的 的 ASCII值
            $d .= $s[($g ^ ord($a[$f + 6])) - $g & 0x1F], //按位亦或，按位与。
            $f++) ;
        return $d;

    }
}

