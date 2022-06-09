<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('test', function () {
    return 'SetRedis, Welcome to LaravelAcademy.org';
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::namespace('Api')->group(function () {
    //用户信息
    Route::post('vcode', 'UserController@vcode');
    Route::post('start_ads', 'AdsController@StartAds');
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    Route::post('random', 'UserController@random');
    Route::post('link', 'UserController@link');

    //开奖结果历史记录
    Route::post('lotterydraw', 'LotteryController@lotterydraw');
    //获取上一期开奖结果
    Route::post('lotterydrawhis', 'LotteryController@lotterydrawhis');
    //获取彩种和值玩法配置
    Route::post('gamesetting', 'LotteryController@gamesetting');
    //获取所有彩种信息
    Route::post('lotterys', 'LotteryController@lotterys');
    //收益排行版
    Route::post('income', 'LotteryController@income');

    Route::post('setVideoRideo', ['uses' => 'SetRedisController@setVideoRideo']);//重新生成视频所有缓存
    //微信h5通知回调
    Route::post('notify', 'NotifyController@notify');
    //验证token，只有登录用户才能访问
    Route::middleware('auto.token')->group(function () {
        Route::post('getuser', 'UserController@getuser');
        Route::post('updateuserinfo', 'UserController@updateuserinfo');
        Route::post('updatepassword', 'UserController@updatepassword');
        Route::post('betOrders', 'UserController@betOrders');
        Route::post('updatemoneypassword', 'UserController@updatemoneypassword');
        Route::post('banklist', 'UserController@banklist');
        Route::post('bankcardadd', 'UserController@bankcardadd');
        Route::post('bankcardlist', 'UserController@bankcardlist');
        Route::post('bankupdate', 'UserController@bankupdate');
        Route::post('orderchange', 'UserController@orderchange');
        Route::post('useraccountchange', 'UserController@useraccountchange');
        Route::post('withdraw', 'UserController@withdraw');
        Route::post('withdrawlist', 'UserController@withdrawlist');
        Route::post('ordersfind', 'UserController@ordersfind');
	Route::post('usergbook', 'UserController@usergbook');
	Route::post('usergbookid', 'UserController@usergbookid');
	Route::post('bankdelete', 'UserController@bankdelete');

        //Route::middleware('auth:api')->group(function () {
        Route::post('test', 'UserController@index'); //test
        Route::post('logout', 'UserController@logout');

        //广告、轮播图
        /*Route::post('navi_ads', 'AdsController@NaviAds');
        Route::post('navi_banner', 'AdsController@NaviBanner');*/
        Route::post('navi_cate', 'AdsController@NavCate');//导航分类
        //视频信息
        Route::post('index_new', 'VideoController@IndexNew');//首页最新
        Route::post('index_recom', 'VideoController@IndexRecom');//首页推荐
        Route::post('navi_video_list', 'VideoController@NaviVideoList');//导航视频列表
        Route::post('label_video_list', 'VideoController@LabelVideoList');//标签视频列表
        Route::post('video_list', 'VideoController@VideoList');//分类视频列表
        Route::post('video_details', 'VideoController@VideoDetails');//视频详情
        Route::post('details_ads', 'AdsController@DetailsAds');//视频详情页广告
        Route::post('category_list', 'VideoController@CategoryList');//视频分类列表
        Route::post('search_video_list', 'VideoController@SearchVideoList');//视频分类列表

        //演员、收藏
        Route::post('actors_list', 'ActorsController@ActorsList');//演员列表
        Route::post('actors_video_list', 'VideoController@ActorsVideoList');//演员视频作品集
        Route::post('actors_colle', 'CollectionController@ActorsCollection');//增加取消明星收藏
        Route::post('video_colle', 'CollectionController@VideoCollection');//增加取消视频收藏
        Route::post('actors_colle_list', 'CollectionController@ActorsCollectionList');//我的明星收藏列表
        Route::post('video_colle_list', 'CollectionController@VideoCollectionList');//我的视频收藏列表
        Route::post('video_watched_list', 'CollectionController@VideoWatchedList');//我的视频收藏列表
        //新闻、公告
        Route::post('notice', 'NewsController@NoticeList');//公告
        //版本
        Route::post('version', 'VersionController@VersionList');
        Route::post('param', 'VersionController@secret_test');
        //商品
        Route::post('product_list', 'ProductController@ProductList');
        //分享
        Route::post('share', 'ShareController@ShareList');
        Route::post('send_invite_code', 'ShareController@SendInviteCode');
        //微信h5支付，生成订单
        Route::post('to_pay', 'PayController@ToPay');
    });

});

Route::get('token_out', function () {
    return response()->json([
        'status' => false,
        'code' => 401,
        'message' => '登录已失效',
        'token' => '',
        'data' => [],
    ])->withHeaders([
        'Content-Type' => 'application/json',
        'api_token' => '',
        'Authorization' => ''
    ]);
});

