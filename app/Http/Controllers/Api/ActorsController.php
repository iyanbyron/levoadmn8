<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SetRedisController;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Site;
use App\Models\Videobigclass;
use App\Models\Videosmallclass;
use App\Models\Navigationsmallclass;
use App\Models\Actors;

use Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;


class ActorsController extends Controller
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
     * 演员列表，
     *
     * */
    public function ActorsList(Request $request)
    {
        $cup_redis = Redis::exists("actors:cup:" . $request->cup);
        if (!$cup_redis) {
            $set_redis = new SetRedisController();
            $set_redis->ActorsRedis();
        }
        $result_data_sting = "actors:cup:" . $request->cup;
        $orderby_desc = env('APP_NAME') . "actors:content:*->collection_num";
        $short_order = $request->short_order;
        //1,人气最高（收藏量），2片量最高
        if ($short_order == 1) {
            $orderby_desc = env('APP_NAME') . "_actors:content:*->collection_num";
        } else {
            $orderby_desc = env('APP_NAME') . "_actors:content:*->video_num";
        }
        //echo $orderby_desc;exit;
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_actors_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        //echo json_encode($orderby_desc);exit;
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page']);
    }


}

