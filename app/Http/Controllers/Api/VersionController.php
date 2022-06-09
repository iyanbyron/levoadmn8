<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SetRedisController;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Version;
use App\Models\Site;
use Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class VersionController extends Controller
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
     * app版本
     *ver_app_type:应用类型1为Android2为iOS
     * */
    public function VersionList(Request $request)
    {
        //Log::debug("dsfsdf");
        $ver_app_type = $request->ver_app_type;
        // Log::info('User failed to login.', ['id' => $ver_app_type]);
        //Log::info('Showing user profile for user: '.$ver_app_type);
        if ($ver_app_type == "") {
            return $this->fail('参数不能为空！', "400");
        }
        $version_redis = Redis::exists("version:content:" . $ver_app_type);
        $version_key_redis = "version:content:" . $ver_app_type;
        $token = Auth::guard($this->guard)->getToken()->get();
        if (!$version_redis) {
            $version_data = Version::where('ver_app_type', '=', $ver_app_type)
                ->orderBy('ver_code', 'desc1')->first();
            if (!empty($version_data)) {
                Redis::hmset($version_key_redis, $version_data->toArray());
            } else {
                return $this->fail('暂无数据！', "400");
            }
        } else {
            $version_data = Redis::hgetall($version_key_redis);
        }
        if (!empty($version_data)) {
            return $this->success($version_data, 'Bearer ' . $token);
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    public function secret_test(Request $request)
    {
        //return $request->all();
        $aa = encryptWithOpenssl(json_encode(['a' => 11]));
        $bb = [
            'res' => $aa,
        ];
        return $this->success($bb, 'Bearer ');
    }
}

