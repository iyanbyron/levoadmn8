<?php

namespace App\Http\Controllers\Api;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Videobigclass;
use App\Models\Navigationsmallclass;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;

class AdsController extends Controller
{
    /**
     * 定义jwt验证$guard参数
     * @var string
     */
    protected $guard = 'api';

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['StartAds']]);
    }

    /**
     * 启动页页广告
     * @return \Illuminate\Http\JsonResponse
     */
    public function StartAds()
    {
        $start_ads_redis = Redis::exists("ads:start_ads");
        if (!$start_ads_redis) {
            $set_redis = new SetRedisController();
            $start_ads = $set_redis->StartAdsRedis();
        } else {
            $start_ads = unserialize(Redis::get("ads:start_ads"));
        }

        if (!empty($start_ads)) {
            return $this->success($start_ads, '');
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    /**
     * 视频，av导航广告-暂时不用
     * @return \Illuminate\Http\JsonResponse
     */
    public function NaviAds()
    {

        $navi_ads_redis = Redis::exists("ads:navi_ads");
        if (!$navi_ads_redis) {
            $navi_ads = Ads::where('ads_position', '=', 2)->orderby('id', 'desc')->get()->toArray();
            $navi_ads ? $navi_ads = $navi_ads : $navi_ads = [];
            Redis::set("ads:navi_ads", serialize($navi_ads));
        } else {
            $navi_ads = unserialize(Redis::get("ads:navi_ads"));
        }

        if (!empty($navi_ads)) {
            return $this->success($navi_ads, '');
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    /**
     * 视频，av导航轮播-暂时不用
     * @return \Illuminate\Http\JsonResponse
     */
    public function NaviBanner()
    {
        $navi_banner_redis = Redis::exists("ads:navi_banner");
        if (!$navi_banner_redis) {
            $navi_banner = Ads::where('ads_position', '=', 3)->orderby('id', 'desc')->get()->toArray();
            foreach ($navi_banner as $key => $val) {
                $navi_banner[$key]['ads_pic'] = [];
                $navi_banner[$key]['ads_url'] = [];
                $navi_banner[$key]['ads_pic'] = explode(',', $val['ads_pic']);
                $navi_banner[$key]['ads_url'] = explode(',', $val['ads_url']);
            }
            $navi_banner ? $navi_banner = $navi_banner : $navi_banner = [];
            Redis::set("ads:navi_banner", serialize($navi_banner));
        } else {
            $navi_banner = unserialize(Redis::get("ads:navi_banner"));
        }

        if (!empty($navi_banner)) {
            return $this->success($navi_banner, '');
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    /**
     * 获取导航中的分类列表,包括一级导航栏目，二级导航栏目，
     * 二级导航轮播，二级导航广告
     * */
    public function NavCate(Request $request)
    {
        $navi_cate_redis = Redis::exists("ads:navi_cate");
        if (!$navi_cate_redis) {

            $set_redis = new SetRedisController();
            $list = $set_redis->NavCateRedis();
        } else {
            $list = unserialize(Redis::get("ads:navi_cate"));
        }
        if (!empty($list)) {
            return $this->success($list, '');
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    /**
     * 视频详情页广告
     * @return \Illuminate\Http\JsonResponse
     */
    public function DetailsAds()
    {
        $start_ads_redis = Redis::exists("ads:details_ads");
        if (!$start_ads_redis) {
            $details_ads = Ads::where('ads_position', '=', 5)->orderby('id', 'desc')->first()->toArray();
            $details_ads ? $details_ads = $details_ads : $details_ads = [];
            Redis::set("ads:details_ads", serialize($details_ads));
        } else {
            $details_ads = unserialize(Redis::get("ads:details_ads"));
        }

        if (!empty($details_ads)) {
            return $this->success($details_ads, '');
        } else {
            return $this->fail('操作失败！', "400");
        }
    }
}

