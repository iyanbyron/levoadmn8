<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SetRedisController;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\News;
use Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;


class NewsController extends Controller
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
     * 最新公告，
     *
     * */
    public function NoticeList(Request $request)
    {
        $notice_redis = Redis::exists("news:content:notice");
        $notice_key_redis = "news:content:notice";
        $token = Auth::guard($this->guard)->getToken()->get();
        if (!$notice_redis) {
            $notice_data = News::orderBy('updated_at', 'desc')
                ->first();
            Redis::hmset($notice_key_redis, $notice_data->toArray());
        } else {
            $notice_data = Redis::hgetall($notice_key_redis);
        }
        if (!empty($notice_data)) {
            return $this->success($notice_data, 'Bearer ' . $token);
        } else {
            return $this->fail('操作失败！', "400");
        }
    }


}

