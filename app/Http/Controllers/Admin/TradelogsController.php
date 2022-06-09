<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserAccountChange;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TradelogsController extends Controller
{
    /**
     * 账变记录
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bigclass_list =[];
        return view('admin.tradelogs.index', compact('bigclass_list'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $all = $request->all();
        $model = UserAccountChange::query();
        if (!empty($all['order'])) {
            $model = $model->where('operator', 'like', '%' . $request->get('operator') . '%');
        }
        if (!empty($all['username'])) {
            $model = $model->where('username', 'like', '%' . $request->get('username') . '%');
        }

        if (!empty($all['time_start'])) {
            $model = $model->where('created_at', '>=', $all['time_start']);
        }
        if (isset($all['time_end']) && !empty($all['time_end'])) {
            $model = $model->where('created_at', '<=', $all['time_end']);
        }

        if (isset($all['type']) && !empty($all['type'])) {
            $model = $model->where('type', $all['type']);
        }

        $res = $model->orderBy('id', 'desc')->paginate($request->get('limit', 15))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }


    /**
     * UserAccountChange
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data_bak(Request $request)
    {
        $all = $request->all();
        //$model = BuyVideo::query();
        $res = BuyVideo::leftjoin('video', 'video.id', '=', 'video_id')
            ->leftjoin('video_bigclass', 'video_bigclass.id', '=', 'buy_video.video_bigclass_id')
            ->select('video.id as video_id', 'video.price', 'video.video_url', 'video.title', 'video_bigclass.big_name', 'uid', 'buy_video.id', 'buy_video.created_at');

        if (!empty($all['uid'])) {
            $res = $res->where('uid', 'like', '%' . $request->get('uid') . '%');
        }
        if (isset($all['video_bigclass_id']) && !empty($all['video_bigclass_id'])) {
            $res = $res->where('buy_video.video_bigclass_id', '=', $all['video_bigclass_id']);
        }

        $res = $res->orderBy('buy_video.id', 'desc')->paginate($request->get('limit', 15))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

}
