<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SetRedisController;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Site;
use App\Models\CollectionActors;
use App\Models\CollectionVideo;
use App\Models\Actors;

use Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;


class CollectionController extends Controller
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
     * 用户增加取消演员收藏，
     *
     * */
    public function ActorsCollection(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $uid = $user['id'];
        $actors_id = $request->actors_id;
        $actors_key_redis = "actors:colle:" . $uid;
        //检查$actors_id是否是$actors_key_redis容器中的成员。
        $data = ['uid' => $uid, 'actors_id' => $actors_id];
        if (Redis::sIsMember($actors_key_redis, $actors_id)) {
            $operation = Redis::sRem($actors_key_redis, $actors_id);
            Redis::expire($actors_key_redis, 60 * 60 * 24 * 60);
            CollectionActors::where('uid', $uid)->where('actors_id', $actors_id)->delete();
        } else {
            $operation = Redis::sadd($actors_key_redis, $actors_id);
            Redis::expire($actors_key_redis, 60 * 60 * 24 * 60);
            CollectionActors::create($data);
        }
        $token = Auth::guard($this->guard)->getToken()->get();
        if ($operation) {
            return $this->success([], 'Bearer ' . $token);
        } else {
            return $this->fail('操作失败！', "400");
        }
    }


    /**
     * @param Request $request :video_id,video_bigclass_id
     * @return \Illuminate\Http\JsonResponse
     * 增加、取消视频收藏
     */
    public function VideoCollection(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $uid = $user['id'];
        $video_id = $request->video_id;
        $video_bigclass_id = $request->video_bigclass_id;
        $video_key_redis = "video:colle_{$video_bigclass_id}:" . $uid;
        //检查$actors_id是否是$actors_key_redis容器中的成员。
        $data = ['uid' => $uid, 'video_id' => $video_id, 'video_bigclass_id' => $video_bigclass_id];
        if (Redis::sIsMember($video_key_redis, $video_id)) {
            $operation = Redis::sRem($video_key_redis, $video_id);
            Redis::expire($video_key_redis, 60 * 60 * 24 * 60);
            CollectionVideo::where('uid', $uid)->where('video_bigclass_id', $video_bigclass_id)->where('video_id', $video_id)->delete();
        } else {
            $operation = Redis::sadd($video_key_redis, $video_id);
            Redis::expire($video_key_redis, 60 * 60 * 24 * 60);
            $is_video_id = CollectionVideo::where('uid', $uid)->where('video_bigclass_id', $video_bigclass_id)->where('video_id', $video_id)->first();
            if (!$is_video_id) {
                CollectionVideo::create($data);
            }
        }
        $token = Auth::guard($this->guard)->getToken()->get();
        if ($operation) {
            return $this->success([], 'Bearer ' . $token);
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    /**
     * 我的演员收藏列表
     *
     * */
    public function ActorsCollectionList(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $uid = $user['id'];
        $result_data_sting = "actors:colle:" . $uid;
        $orderby_desc = env('APP_NAME') . "actors:content:*->id";
        //echo json_encode($orderby_desc);exit;
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_actors_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page'], $list['total']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page'], $list['total']);
    }


    /**
     * 我的视频收藏列表
     * $request->video_bigclass_id
     * */
    public function VideoCollectionList(Request $request)
    {
        $video_bigclass_id = $request->video_bigclass_id;
        $user = JWTAuth::parseToken()->authenticate();
        $uid = $user['id'];
        $result_data_sting = "video:colle_{$video_bigclass_id}:" . $uid;
        $orderby_desc = env('APP_NAME') . "video:content:*->id";
        //echo json_encode($orderby_desc);exit;
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_video_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page'], $list['total']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page'], $list['total']);
    }

    /**
     * 我的视频观看列表
     * $request->video_bigclass_id
     * */
    public function VideoWatchedList(Request $request)
    {
        $video_bigclass_id = $request->video_bigclass_id;
        $user = JWTAuth::parseToken()->authenticate();
        $uid = $user['id'];
        $result_data_sting = "video:watched_{$video_bigclass_id}:" . $uid;
        $orderby_desc = env('APP_NAME') . "video:content:*->id";
        //echo json_encode($orderby_desc);exit;
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_video_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page'], $list['total']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page'], $list['total']);
    }
}

