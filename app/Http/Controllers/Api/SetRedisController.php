<?php

namespace App\Http\Controllers\Api;

use App\Models\Ads;
use App\Models\Paychannel;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\Video;
use App\Models\Actors;
use App\Models\Label;
use App\Models\Site;
use App\Models\Videobigclass;
use App\Models\Videosmallclass;
use App\Models\Navigationsmallclass;
use Illuminate\Support\Facades\Redis;

class SetRedisController extends Controller
{

    /**
     * 重新生成视频所有缓存
     * */

    /*redis实现分页
    使用有序集合zadd
    使用hash存储具体的数据
    取数据通过有序集合的ZREVRANGE---递减排序
    //按时间降序进行排序
    //存数据
    $redis->zAdd($key,$article['add_time'],'article:'.$artice['id']);
    $redis->hMset('article'.$article['id'],$article);

    //取数据
    $result = $redis->zRevRange($key,$start,$start+10,true);  //获取到数据，每一次10条数据
    $count = $redis->ZCARD($key);  //获取总条数

    foreach($result as $k=>$v){
    $article = $reids->hGetAll($k)  //每一篇文章的具体数据
    }*/

    public function setVideoRideo()
    {

        //生成sid视频id
        $bigclass_data = Videobigclass::orderBy('id', 'asc')
            ->get(['id'])->toArray();
        foreach ($bigclass_data as $key_big => $value_big) {
            //一级分类写入缓存
            $list_vid = [];
            $list_vid = Video::orderBy('id', 'desc')
                ->where('video_bigclass_id', '=', $value_big['id'])
                ->get(['id', 'created_at'])->toArray();
            foreach ($list_vid as $key_vid => $value_vid) {
                Redis::sadd("video:bids:" . $value_big['id'], $value_vid['id']);
            }
            //二级分类写入缓存
            $smallclass_data = Videosmallclass::orderBy('id', 'asc')
                ->where('video_bigclass_id', '=', $value_big['id'])
                ->get(['id'])->toArray();
            foreach ($smallclass_data as $key => $value) {
                $list_id = [];
                $list_id = Video::orderBy('id', 'desc')
                    ->where('video_smallclass_id', '=', $value['id'])
                    ->get(['id'])->toArray();
                foreach ($list_id as $key1 => $value1) {
                    Redis::sadd("video:sids_{$value_big['id']}:" . $value['id'], $value1['id']);
                    Redis::sadd("video:sids_{$value_big['id']}:all", $value1['id']);
                    Redis::sadd("video:sids:" . $value['id'], $value1['id']);
                }
            }

            //二级导航分类写入缓存
            $navismallclass_data = Navigationsmallclass::orderBy('id', 'asc')
                ->where('video_bigclass_id', '=', $value_big['id'])
                ->get(['id'])->toArray();
            foreach ($navismallclass_data as $key => $value) {
                $list_nid = [];
                $list_nid = Video::orderBy('id', 'desc')
                    ->where('navi_smallclass_id', '=', $value['id'])
                    ->get(['id'])->toArray();
                foreach ($list_nid as $key1 => $value1) {
                    Redis::sadd("video:nids_{$value_big['id']}:" . $value['id'], $value1['id']);
                    //1000：所有二级导航视频（可生成最新，排行视频列表）
                    Redis::sadd("video:nids_{$value_big['id']}:all", $value1['id']);
                    Redis::sadd("video:nids:" . $value['id'], $value1['id']);
                }
            }

            //首页最新视频(读取最后更新前一天的记录，显示60条)
            $last_update_time = Video::orderBy('id', 'desc')->first(['id', 'updated_at'])->toArray();
            //$yesterday_time = strtotime("-1days", strtotime($last_update_time['updated_at']));
            $today_time = strtotime($last_update_time['updated_at']);
            $yesterday_time = date('Y-m-d 00:00:00', $today_time);
            $list_vid = [];
            $list_vid = Video::where('updated_at', '<', $yesterday_time)
                ->where('is_recom', 0)
                ->where('video_bigclass_id', '=', $value_big['id'])
                ->take(60)
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'created_at'])->toArray();
            foreach ($list_vid as $key_vid => $value_vid) {
                Redis::sadd("video:index_new:" . $value_big['id'], $value_vid['id']);
            }

            //首页推荐(精选)视频(读取最后推荐的60条记录)
            $list_vid = [];
            $list_vid = Video::where('is_recom', 1)
                ->where('video_bigclass_id', '=', $value_big['id'])
                ->take(60)
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'created_at'])->toArray();
            foreach ($list_vid as $key_vid => $value_vid) {
                Redis::sadd("video:index_recom:" . $value_big['id'], $value_vid['id']);
            }

            //有码,无码
            $mosaic_data = [0, 1];
            foreach ($mosaic_data as $key => $value) {
                $list_vid = [];
                $list_vid = Video::where('is_mosaic', $value)
                    ->where('video_bigclass_id', '=', $value_big['id'])
                    ->orderBy('updated_at', 'desc')
                    ->get(['id', 'created_at'])->toArray();
                foreach ($list_vid as $key_vid => $value_vid) {
                    Redis::sadd("video:mosaic_{$value_big['id']}:" . $value, $value_vid['id']);
                    Redis::sadd("video:mosaic_{$value_big['id']}:all", $value_vid['id']);
                }
            }

            /*//有码
            $list_vid = [];
            $list_vid = Video::where('is_mosaic', 1)
                ->where('video_bigclass_id', '=', $value_big['id'])
                ->orderBy('updated_at', 'desc')
                ->get(['id','created_at'])->toArray();
            foreach ($list_vid as $key_vid => $value_vid) {
                Redis::sadd("video:mosaic:1:" . $value_big['id'], $value_vid['id']);
            }
            //无码
            $list_vid = [];
            $list_vid = Video::where('is_mosaic', 0)
                ->where('video_bigclass_id', '=', $value_big['id'])
                ->orderBy('updated_at', 'desc')
                ->get(['id','created_at'])->toArray();
            foreach ($list_vid as $key_vid => $value_vid) {
                Redis::sadd("video:mosaic:0:" . $value_big['id'], $value_vid['id']);
            }*/

            //类别序列化
            $list_category = [];
            $list_category = Videosmallclass::where('video_bigclass_id', '=', $value_big['id'])
                ->orderBy('id', 'desc')
                ->get()->toArray();
            Redis::set("category:" . $value_big['id'], serialize($list_category));
            //类别hash
            $list_category = [];
            $list_category = Videosmallclass::where('video_bigclass_id', '=', $value_big['id'])
                ->orderBy('id', 'desc')
                ->get()->toArray();
            foreach ($list_category as $key => $value) {
                Redis::hmset("category:content_{$value_big['id']}:" . $value['id'], $value);
            }

            //标签
            $label_data = Label::orderBy('id', 'asc')
                ->where('video_bigclass_id', '=', $value_big['id'])
                ->get(['id'])->toArray();
            foreach ($label_data as $key => $value) {
                $list_nid = [];
                $list_nid = Video::orderBy('id', 'desc')
                    ->whereRaw('FIND_IN_SET(?,label_id)', [$value['id']])
                    ->get(['id'])->toArray();
                foreach ($list_nid as $key1 => $value1) {
                    Redis::sadd("video:label_{$value_big['id']}:" . $value['id'], $value1['id']);
                    //Redis::sadd("video:label:" . $value['id'], $value1['id']);
                }
            }

        }

        //罩杯视频id -------------
        $actors_data = Actors::orderBy('id', 'asc')
            ->get(['id', 'actors_cup'])->toArray();
        foreach ($actors_data as $key_actors => $value_actors) {
            $list_vid = [];
            $list_vid = Video::whereRaw('FIND_IN_SET(?,actors_id)', [$value_actors['id']])
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'created_at'])->toArray();
            foreach ($list_vid as $key_vid => $value_vid) {
                Redis::sadd("video:cup:" . $value_actors['actors_cup'], $value_vid['id']);
                Redis::sadd("video:cup:all", $value_vid['id']);
                //演员视视频作品集
                Redis::sadd("video:actors:" . $value_actors['id'], $value_vid['id']);
            }
        }


        //视频详情
        $this->VideoRedis();
        //标签详情内容
        $list_lid = [];
        $list_lid = Label::orderBy('id', 'desc')
            ->get()->toArray();
        foreach ($list_lid as $key_lid => $value_lid) {
            Redis::hmset("label:content:" . $value_lid['id'], $value_lid);
        }
        //演员缓存生成
        $this->ActorsRedis();
        //商品
        $pr_type_data = [1, 2];//支付类型1vip,2金币
        foreach ($pr_type_data as $key => $value) {
            $list_vid = [];
            $list_vid = Product::where('pr_type', $value)
                ->where('pr_status', '=', 1)
                ->orderBy('pr_rank', 'desc')
                ->get()->toArray();
            foreach ($list_vid as $key_vid => $value_vid) {
                Redis::sadd("product:type_{$value}", $value_vid['id']);
                Redis::hmset("product:content:" . $value_vid['id'], $value_vid);
            }
        }
        //支付渠道
        $list_data = [];
        $list_data = Paychannel::orderBy('id', 'desc')
            ->get()->toArray();
        foreach ($list_data as $key => $value) {
            Redis::hmset("paychannel:content:" . $value['pay_type'], $value);
        }

        return response()->json(['msg' => '更新视频缓存成功', 'code' => 200, 'info' => []]);

    }

    /**
     * 演员redis
     */
    public function ActorsRedis()
    {
        //演员罩杯分类
        $actors_data = [];
        $actors_data = Actors::groupBy('actors_cup')
            ->get(['actors_cup'])->toArray();
        foreach ($actors_data as $key_actors => $value_actors) {
            $list_aid = [];
            $list_aid = Actors::where('actors_cup', '=', $value_actors['actors_cup'])
                ->orderBy('id', 'asc')
                ->get(['id', 'created_at'])->toArray();
            foreach ($list_aid as $key_aid => $value_aid) {
                Redis::sadd("actors:cup:" . $value_actors['actors_cup'], $value_aid['id']);
                Redis::sadd("actors:cup:all", $value_aid['id']);
            }
        }

        //演员详情
        $domain = unserialize(Redis::get("system:sites"));
        if (!$domain) {
            $domain_data = Site::first();
            $domain = json_decode($domain_data['value'], true);
            Redis::set("system:sites", serialize($domain));
        }
        $list_aid = [];
        $list_aid = Actors::orderBy('id', 'desc')
            ->get()->toArray();
        foreach ($list_aid as $key_aid => $value_aid) {
            $value_aid['actors_pic'] = $domain['img_domain'] . '/' . $value_aid['actors_pic'];
            Redis::hmset("actors:content:" . $value_aid['id'], $value_aid);
        }
    }

    /**
     * 视频详情缓存
     */
    public function VideoRedis()
    {
        //视频详情
        $domain = unserialize(Redis::get("system:sites"));
        if (!$domain) {
            $domain_data = Site::first();
            $domain = json_decode($domain_data['value'], true);
            Redis::set("system:sites", serialize($domain));
        }
        $list_vid = [];
        $list_vid = Video::orderBy('id', 'desc')
            ->get()->toArray();
        foreach ($list_vid as $key_vid => $value_vid) {
            $value_vid['img'] = $domain['img_domain'] . '/' . $value_vid['img_url'];
            $value_vid['video'] = $domain['video_domain'] . '/' . $value_vid['video_url'];
            Redis::hmset("video:content:" . $value_vid['id'], $value_vid);
        }
    }

    /**
     * 启动页广告缓存
     */
    public function StartAdsRedis()
    {
        $domain = unserialize(Redis::get("system:sites"));
        if (!$domain) {
            $domain_data = Site::first();
            $domain = json_decode($domain_data['value'], true);
            Redis::set("system:sites", serialize($domain));
        }
        $start_ads = Ads::where('ads_position', '=', 1)->orderby('id', 'desc')->first()->toArray();
        $start_ads ? $start_ads = $start_ads : $start_ads = [];
        if (!empty($start_ads)) {
            $start_ads_arr = explode(',', $start_ads['ads_pic']);
            $start_ads['ads_pic'] = [];
            foreach ($start_ads_arr as $key => $value) {
                $start_ads['ads_pic'][$key] = $domain['img_domain'] . '/' . $value;
            }

        }
        Redis::set("ads:start_ads", serialize($start_ads));
        return $start_ads;
    }

    /**
     * 获取导航中的分类列表,包括一级导航栏目，二级导航栏目，
     * 二级导航轮播，二级导航广告
     */
    public function NavCateRedis()
    {
        $domain = unserialize(Redis::get("system:sites"));
        if (!$domain) {
            $domain_data = Site::first();
            $domain = json_decode($domain_data['value'], true);
            Redis::set("system:sites", serialize($domain));
        }
        //$domain['img_domain']

        $big_nav_list = Videobigclass::orderBy('id', 'asc')
            ->get(['id', 'big_name', 'big_introduction'])->toArray();
        foreach ($big_nav_list as $key => $value) {
            $bigclass = array();
            $bigclass = Videobigclass::where('id', $value['id'])
                ->first(['id as nav_big_id', 'big_name']);
            $list[$key] = $bigclass;
            $navi_small_data = array();
            $navi_small_data = Navigationsmallclass::where('video_bigclass_id', $value['id'])
                ->orderBy('id', 'asc')
                ->get(['id as nav_small_id', 'small_name'])->toArray();
            $navi_small_hot =
                [
                    'nav_small_id' => 999,
                    'small_name' => '精选',
                ];
            $navi_small_new =
                [
                    'nav_small_id' => 1000,
                    'small_name' => '最新',
                ];
            //头部追加精选和最新
            array_unshift($navi_small_data, $navi_small_hot, $navi_small_new);
            foreach ($navi_small_data as $keyt => $valt) {
                //$smalltype[$keyt]['api'] = 'getVideoList';
                $banner = array();
                $banner = Ads::where('ads_position', 3)
                    ->where('video_bigclass_id', $value['id'])
                    ->where('ads_status', 1)
                    ->orderBy('id', 'asc')
                    ->get(['ads_title', 'ads_pic', 'ads_url', 'navigation_smallclass_id'])->toArray();
                foreach ($banner as $key_banner => $value_banner) {
                    $banner[$key_banner]['ads_pic'] = $domain['img_domain'] . '/' . $value_banner['ads_pic'];
                }
                $ads = array();
                $ads = Ads::where('ads_position', 2)
                    ->where('video_bigclass_id', $value['id'])
                    ->where('navigation_smallclass_id', $valt['nav_small_id'])
                    ->where('ads_status', 1)
                    ->orderBy('id', 'asc')
                    ->first(['ads_title', 'ads_pic', 'ads_url', 'navigation_smallclass_id']);
                if (!empty($ads)) {
                    $ads['ads_pic'] = $domain['img_domain'] . '/' . $ads['ads_pic'];
                }

                if (empty($ads)) $ads = '';
                $navi_small_data[$keyt]['ads'] = $ads;
                $navi_small_data[$keyt]['banner'] = $banner;

            }
            $list[$key]['small_navi'] = $navi_small_data;
        }
        $list = json_decode(json_encode($list), true);
        Redis::set("ads:navi_cate", serialize($list));
        return $list;
    }

}
