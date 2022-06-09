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
use App\Models\BuyVideo;

use Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;


class VideoController extends Controller
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
     * AV、视频导航首页最新视频
     * @return \Illuminate\Http\JsonResponse
     */
    public function IndexNew(Request $request)
    {
        $index_new_redis = Redis::exists("video:index_new:" . $request->bigclass_id);
        if (!$index_new_redis) {
            $set_redis = new SetRedisController();
            $set_redis->setVideoRideo();
        }
        $result_data_sting = "video:index_new:" . $request->bigclass_id;
        $orderby_desc = env('APP_NAME') . "_video:content:*->id";
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_video_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);

        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page']);


    }

    /**
     * AV、视频导航首页推荐视频
     * @return \Illuminate\Http\JsonResponse
     */
    public function IndexRecom(Request $request)
    {
        $index_recom_redis = Redis::exists("video:index_recom:" . $request->bigclass_id);
        if (!$index_recom_redis) {
            $set_redis = new SetRedisController();
            $set_redis->setVideoRideo();
        }
        $result_data_sting = "video:index_recom:" . $request->bigclass_id;
        $orderby_desc = env('APP_NAME') . "_video:content:*->id";
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_video_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);

        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page']);


    }


    /**
     * 二级导航视频列表，
     *
     * */
    public function NaviVideoList(Request $request)
    {
        $navi_redis = Redis::exists("video:bids:" . $request->bigclass_id);
        if (!$navi_redis) {
            $set_redis = new SetRedisController();
            $set_redis->setVideoRideo();
        }
        $result_data_sting = "video:nids_{$request->bigclass_id}:" . $request->navi_smallclass_id;
        $orderby_desc = env('APP_NAME') . "_video:content:*->id";
        //1000：导航最新视频，1001：导航排行视频
        if ($request->navi_smallclass_id = 1000) {
            $result_data_sting = "video:nids_{$request->bigclass_id}:all";
            $orderby_desc = env('APP_NAME') . "_video:content:*->updated_at";
        }
        if ($request->navi_smallclass_id = 1001) {
            $result_data_sting = "video:nids_{$request->bigclass_id}:all";
            $orderby_desc = env('APP_NAME') . "_video:content:*->hits";
        }
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_video_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page']);
    }

    /**
     * 二级标签视频列表，
     *
     * */
    public function LabelVideoList(Request $request)
    {
        $navi_redis = Redis::exists("video:bids:" . $request->bigclass_id);
        if (!$navi_redis) {
            $set_redis = new SetRedisController();
            $set_redis->setVideoRideo();
        }
        $result_label_sting = "video:label_{$request->bigclass_id}:" . $request->label_id;
        $result_data_sting = $result_label_sting;
        $orderby_desc = env('APP_NAME') . "_video:content:*->id";
        $short_order = $request->short_order;
        //有码:1或无码：0 与小类标签视频交集
        if ($short_order == 1) {
            $orderby_desc = env('APP_NAME') . "_video:content:*->id";
        } else {
            $orderby_desc = env('APP_NAME') . "_video:content:*->hits";
        }

        $result_mosaic_sting = "video:mosaic_{$request->bigclass_id}:" . $request->is_mosaic;
        $result_data_sting = "result:lable_{$request->bigclass_id}:" . $request->label_id . "-and-isMosaic:{$request->is_mosaic}";
        Redis::sInterStore($result_data_sting, $result_label_sting, $result_mosaic_sting);

        //echo json_encode($orderby_desc);exit;
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_video_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        //echo json_encode($orderby_desc);exit;
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page']);
    }

    /**
     * 分类视频列表，
     *
     * */
    public function VideoList(Request $request)
    {
        $navi_redis = Redis::exists("video:bids:" . $request->bigclass_id);
        if (!$navi_redis) {
            $set_redis = new SetRedisController();
            $set_redis->setVideoRideo();
        }
        $result_smallclass_sting = "video:sids_{$request->bigclass_id}:" . $request->smallclass_id;
        $result_mosaic_sting = "video:mosaic_{$request->bigclass_id}:" . $request->is_mosaic;
        $result_cup_sting = "video:cup:" . $request->cup;
        //$result_data_sting = $result_smallclass_sting;
        $orderby_desc = env('APP_NAME') . "_video:content:*->id";
        $short_order = $request->short_order;
        //有码:1或无码：0 与小类视频交集
        if ($short_order == 1) {
            $orderby_desc = env('APP_NAME') . "_video:content:*->id";
        } else {
            $orderby_desc = env('APP_NAME') . "_video:content:*->hits";
        }
        // echo json_encode($orderby_desc);exit;
        //if($request->is_mosaic<>"")
        $result_data_sting = "result:{$request->bigclass_id}:" . $request->smallclass_id . "-cup-isMosaic";
        Redis::sInterStore($result_data_sting, $result_smallclass_sting, $result_mosaic_sting, $result_cup_sting);
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_video_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        //echo json_encode($orderby_desc);exit;
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page']);
    }

    /**
     * 获取视频详情
     * */
    public function VideoDetails(Request $request)
    {
        $id = $request->id;
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user['user_id'];
        $uid = $user['id'];
        $token = Auth::guard($this->guard)->getToken()->get();
        //获取视频详情内容
        $video_key = "video:content:" . $id;
        $video_view = Redis::hgetall($video_key);
        $small_name_data = Redis::hgetall("category:content_{$video_view['video_bigclass_id']}:" . $video_view['video_smallclass_id']);
        $video_view['small_name'] = $small_name_data['small_name'];
        $video_view['updated_at'] = date('Y年m月d日', strtotime($video_view['updated_at'])) . '上架';
        Video::where('id', $video_view['id'])->increment('hits', 1);
        Redis::hIncrBy($video_key, 'hits', 1);//更新点击次数
        $data['video'] = $video_view;
        //获取用户购买视频数据
        //$is_buyvideo_data = Redis::hgetall("video:buy:vid{$video_view['id']}-userid" . $uid);
        $buyvideo_key_redis = "video:buy_{$video_view['video_bigclass_id']}:" . $uid;
        if (!Redis::sIsMember($buyvideo_key_redis, $video_view['id'])) {
            $is_buyvideo_data = BuyVideo::where('video_id', $video_view['id'])
                ->where('video_bigclass_id', $video_view['video_bigclass_id'])
                ->where('uid', '=', $uid)
                ->first();
            if ($is_buyvideo_data) {
                Redis::sadd($buyvideo_key_redis, $video_view['id']);
                Redis::expire($buyvideo_key_redis, 60 * 60 * 24 * 30 * 2);
            }
        }
        //获取用户信息
        $vip_data = Redis::hgetall("user:info:{$uid}");
        if (!$vip_data) {
            $vip_data = Member::where('id', '=', $uid)
                ->first();
            if ($vip_data) {
                if (strtotime($vip_data['vip_end_time']) < time()) {
                    $vip_data['vip_end_time'] = "";
                    $vip_data['vip_is_end'] = 1;
                    $data['is_vip'] = 0;
                } else {
                    $vip_data['vip_is_end'] = 0;
                    $data['is_vip'] = 1;
                }
                Redis::hmset("user:info:{$uid}", $vip_data->toArray());
                Redis::expire("user:info:{$uid}", 60 * 60 * 24 * 1);
                Redis::hmset("user:info:{$user_id}", $vip_data->toArray());
                Redis::expire("user:info:{$user_id}", 60 * 60 * 24 * 1);
            }
        }
        if (strtotime($vip_data['vip_end_time']) > time()) {
            $is_vip = 1;
        } else {
            $is_vip = 0;
        }
        $is_watch = 0;
        if ($is_buyvideo_data) $is_watch = 1;
        if ($is_vip == 1) $is_watch = 1;
        $data['video']['is_watch'] = $is_watch;
        //增加观看记录
        if ($is_watch == 1) {
            $video_key_redis = "video:watched_{$video_view['video_bigclass_id']}:" . $uid;
            Redis::sadd($video_key_redis, $video_view['id']);
            Redis::expire($video_key_redis, 60 * 60 * 24 * 30);
        }
        $domain = unserialize(Redis::get("system:sites"));
        if (!$domain) {
            $domain_data = Site::first();
            $domain = json_decode($domain_data['value'], true);
            Redis::set("system:sites", serialize($domain));
        }
        //推荐视频缓存处理
        $video_recom_key = "video:sids_{$video_view['video_bigclass_id']}:{$video_view['video_smallclass_id']}";
        //$video_recommend_redis = Redis::smembers($video_recom_key);
        $orderby_desc = env('APP_NAME') . "_video:content:*->hits";
        //获取视频详情列表数据
        $video_recommend = redis_video_detail($video_recom_key, $orderby_desc, 20);
        $data['video_recommend'] = $video_recommend;
        //获取演员
        $actors_id_data = explode(',', $video_view['actors_id']);
        foreach ($actors_id_data as $key_actorsid => $value_actorsid) {
            $actors_data[] = Redis::hgetall("actors:content:" . $value_actorsid);
        }
        $data['actors'] = $actors_data;
        //获取标签
        $label_id_data = explode(',', $video_view['label_id']);
        foreach ($label_id_data as $key_labelid => $value_labelid) {
            $label_data[] = Redis::hgetall("label:content:" . $value_labelid);
        }
        $data['label'] = $label_data;

        if (!empty($data)) {
            return $this->success($data, 'Bearer ' . $token);
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    /**
     * 视频二级分类目录列表
     * */
    public function CategoryList(Request $request)
    {
        $bigcalss_id = $request->bigcalss_id;
        $token = Auth::guard($this->guard)->getToken()->get();
        $category_info_redis = Redis::exists("category:" . $bigcalss_id);
        if (!$category_info_redis) {
            $list_category = Videosmallclass::where('video_bigclass_id', '=', $bigcalss_id)
                ->orderBy('id', 'desc')
                ->get()->toArray();
            Redis::set("category:" . $bigcalss_id, serialize($list_category));
            $list_category ? $list_category = $list_category : $list_category = [];
        } else {
            $list_category = unserialize(Redis::get("category:" . $bigcalss_id));
        }
        if (!empty($list_category)) {
            return $this->success($list_category, 'Bearer ' . $token);
        } else {
            return $this->fail('操作失败！', "400");
        }
    }

    /**
     * 演员作品集
     * $request->actors_id
     * */
    public function ActorsVideoList(Request $request)
    {
        $actors_redis = Redis::exists("video:actors:" . $request->actors_id);
        if (!$actors_redis) {
            $set_redis = new SetRedisController();
            $set_redis->setVideoRideo();
        }
        $cdata['actors'] = Redis::hgetall("actors:content:" . $request->actors_id);;
        $result_data_sting = "video:actors:" . $request->actors_id;
        $orderby_desc = env('APP_NAME') . "_video:content:*->id";
        //echo json_encode($orderby_desc);exit;
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        $list = redis_video_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        //echo json_encode($orderby_desc);exit;
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page'], $list['total']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page'], $list['total'], $cdata);
    }

    /**
     * 二级标签视频列表，
     *
     * */
    public function SearchVideoList(Request $request)
    {
        $keyword = $request->keyword;
        $video_bigclass_id = $request->video_bigclass_id;
        $page = $request->page;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = $request->pagesize;
        if (empty($keyword)) {
            return response()->json(['msg' => 'fid不能为空', 'code' => 404, 'info' => []]);
        }
        $domain = unserialize(Redis::get("system:sites"));
        if (!$domain) {
            $domain_data = Site::first();
            $domain = json_decode($domain_data['value'], true);
            Redis::set("system:sites", serialize($domain));
        }
        $list = Video::where('video_bigclass_id', $video_bigclass_id)
            //->where('title','like',"%{$keyword}%")
            ->orderBy('created_at', 'desc')
            ->paginate($pagesize)
            ->toArray();
        foreach ($list['data'] as $key => $val) {
            $list['data'][$key]['img_url'] = $domain['img_domain'] . '/' . '/' . $val['img_url'];
            $list['data'][$key]['video_url'] = $domain['video_domain'] . '/' . $val['video_url'];
        }
        /*
                $av_list = Video::where('video_bigclass_id', 1)
                    //->where('title','like',"%{$keyword}%")
                    ->orderBy('created_at', 'desc')
                    ->paginate($pagesize)
                    ->toArray();
                foreach ($av_list['data'] as $key => $val) {
                    $av_list['data'][$key]['img_url'] = $domain['img_domain'] . $val['img_url'];
                    $av_list['data'][$key]['video_url'] = $domain['video_domain'] .'/'. $val['video_url'];
                }
                echo json_encode($av_list);exit;*/
        /*$video_list = Video::where('video_bigclass_id', 2)
            //->where('title','like',"%{$keyword}%")
            ->orderBy('created_at', 'desc')
            ->paginate($pagesize)
            ->toArray();
        foreach ($video_list['data'] as $key => $val) {
            $video_list['data'][$key]['img_url'] = $domain['img_domain'] . $val['img_url'];
            $video_list['data'][$key]['video_url'] = $domain['video_domain'] .'/'. $val['video_url'];
        }
        $list['av'] = $av_list;
        $list['video'] = $video_list;*/
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page'], $list['total']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page'], $list['total']);
    }
}

