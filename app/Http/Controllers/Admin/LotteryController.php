<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\SetRedisController;
use App\Http\Requests\LotteryUpdateRequest;
use App\Models\Lottery;
use App\Models\BetOrders;

use App\Models\Games;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LotteryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.lottery.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request)
    {   //DB::connection()->enableQueryLog();  // 开启QueryLog
        $all = $request->all();
        $res = Lottery::leftjoin('games', 'lottery.game_id', '=', 'games.id')
            //->leftjoin('navigation_smallclass', 'ads.navigation_smallclass_id', '=', 'navigation_smallclass.id')
            //->leftjoin('video_smallclass', 'ads.video_smallclass_id', '=', 'video_smallclass.id')
            ->select('lottery.*', 'games.game_name');
        if (!empty($all['game_name'])) {
            $res = $res->where('games.game_name', $all['game_name']);
        }
        if (!empty($all['issue'])) {
            $res = $res->where('lottery.issue', $all['issue']);
        }

        if (!empty($all['time_start'])) {
            $yestoday_time=date("Y-m-d 00:00:00", strtotime("+1 day", strtotime( $all['time_start'])));
            $res = $res->where('lottery.open_time', '>=', $all['time_start']);
            $res = $res->where('lottery.open_time', '<', $yestoday_time);
        }

        $res = $res->orderBy('id', 'desc')->paginate($request
            ->get('limit', 20))->toArray();
        //dump(DB::getQueryLog());exit;
        //print_r($res);exit;
        foreach ($res['data'] as $key => $val) {
            if($val['win_number']<>"")
            {
                $sum_value=0;
                $sum_value=substr($val['win_number'],0,1)+substr($val['win_number'],1,1)+substr($val['win_number'],2,1);
                $res['data'][$key]['sum_value'] =$sum_value;
                $sum_bet_money=0;
                $sum_bet_money = BetOrders::where('game_id', '=', $val['game_id'])->where('issue', '=', $val['issue'])->sum('bet_money');
                $res['data'][$key]['bet_money'] =$sum_bet_money;
                $sum_win_money=0;
                $sum_win_money = BetOrders::where('game_id', '=', $val['game_id'])->where('issue', '=', $val['issue'])->sum('win_money');
                $res['data'][$key]['win_money'] =$sum_win_money;

            }
        }
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data = ['ads_position' => '0', 'navigation_smallclass_id' => '0', 'video_smallclass_id' => '0', 'video_bigclass_id' => '0', 'id' => '0', 'ads_status' => '0'];
        $ads_pic_list = [];
        $data = (object)$data;
        $bigclass_list = Videobigclass::select()->get();
        $bigclass_list_arr = $bigclass_list->toArray();
        $def_navismallclass = Navigationsmallclass::where('video_bigclass_id', '=', $bigclass_list_arr[0]['id'])->get();
        $def_navismallclass[] =
            [
                'id' => 999,
                'small_name' => '精选',
                'big_name' => '',
                'created_at' => '',
                'updated_at' => '',
                'video_bigclass_id' => $def_navismallclass[0]['video_bigclass_id']
            ];
        $def_navismallclass[] =
            [
                'id' => 1000,
                'small_name' => '最新',
                'big_name' => '',
                'created_at' => '',
                'updated_at' => '',
                'video_bigclass_id' => $def_navismallclass[0]['video_bigclass_id']
            ];
        //echo json_encode($def_navismallclass);exit;
        $def_smallclass = Videosmallclass::where('video_bigclass_id', '=', $bigclass_list_arr[0]['id'])->get();

        return view('admin.lottery.create', compact('data', 'bigclass_list', 'def_navismallclass', 'def_smallclass', 'ads_pic_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(AdsCreateRequest $request)
    {
        $data = $request->all();
        if (empty($data['ads_pic'])) {
            return response()->json([
                'status' => 'fail',
                'message' => '请上传图片'
            ]);
        }
        if ($data['ads_position'] == 1) {
            $data['ads_pic'] = implode(',', $data['ads_pic']);
        } else {
            $data['ads_pic'] = end($data['ads_pic']);
        }

        if ($ads_data = Ads::create($data)) {
            if (request()->ajax()) {
                /*if($data['ads_position']==1)
                {
                    $start_ads=Ads::where('ads_position','=',1)->orderby('id','desc')->first()->toArray();
                    Redis::set("ads:start_ads}", serialize($start_ads));
                }
                if($data['ads_position']==2)
                {
                    $start_ads=Ads::where('ads_position','=',2)->orderby('id','desc')->limit(2)->get()->toArray();
                    Redis::set("ads:navi_ads", serialize($start_ads));
                }*/
                $set_redis = new SetRedisController();
                $list = $set_redis->NavCateRedis();
                return response()->json([
                    'status' => 'success',
                    'message' => '添加广告成功'
                ]);
            } else {
                return redirect()->to(route('admin.ads'))->with(['status' => '添加广告成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.ads'))->withErrors('系统错误');
        }

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Lottery::findOrFail($id);

        return view('admin.lottery.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $lottery = Lottery::findOrFail($id);
        $data = $request->except('_method', '_token', '_url');
        if (!empty($lottery->update($data))) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '操作成功'
                ]);
            } else {
                return redirect()->to(route('admin.lottery'))->with(['status' => '操作成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.lottery'))->withErrors('系统错误');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return response()->json(['code' => 1, 'msg' => '请选择删除项']);
        }
        if (Ads::destroy($ids)) {
            $set_redis = new SetRedisController();
            $list = $set_redis->NavCateRedis();
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return response()->json(['code' => 1, 'msg' => '删除失败']);
    }

    /**
     * 根据一级导航分类id获取视频分类列表
     *
     * @return Response
     */
    public function smallclasslist(Request $request)
    {

        $video_bigclass_id = $request->input('video_bigclass_id', '');
        //查询导航小类
        $navi_handle = Navigationsmallclass::orderBy('id', 'asc');
        if (!empty($video_bigclass_id)) {
            $navi_handle->where('video_bigclass_id', '=', $video_bigclass_id);
        }
        $navismallclass = $navi_handle->get()->toArray();
        $navismallclass[] =
            [
                'id' => 999,
                'small_name' => '精选',
                'big_name' => '',
                'created_at' => '',
                'updated_at' => '',
                'video_bigclass_id' => $video_bigclass_id
            ];
        $navismallclass[] =
            [
                'id' => 1000,
                'small_name' => '最新',
                'big_name' => '',
                'created_at' => '',
                'updated_at' => '',
                'video_bigclass_id' => $video_bigclass_id
            ];
        $data['navismallclass'] = $navismallclass;

        //查询视频小类
        $video_handle = Videosmallclass::orderBy('id', 'asc');
        if (!empty($video_bigclass_id)) {
            $video_handle->where('video_bigclass_id', '=', $video_bigclass_id);
        }
        $data['smallclass'] = $video_handle->get()->toArray();

        return json_encode($data);
    }

}
