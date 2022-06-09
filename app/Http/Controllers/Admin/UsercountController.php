<?php

namespace App\Http\Controllers\Admin;

use App\Models\BetOrders;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\Withdrawal;
class UsercountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $superior_id=$request->get('superior_id');
        $data = ['superior_id' => $superior_id];
        $data = (object)$data;
        return view('admin.usercount.index', compact('data'));
    }

    public function data(Request $request)
    {
        $all = $request->all();
        $model = Member::query()->where('user_type',1);
        if (!empty($all['username'])) {
            $model = $model->where('username', 'like', '%' . $request->get('username') . '%');
        }
        if (!empty($all['superior_id'])) {
            $user_data=Member::where('id',$all['superior_id'])->first();
            $model = $model->where('superior', $user_data->username);
        }
        if (!empty($all['time_start'])) {
            $model = $model->where('created_at', '>=', $all['time_start']);
        }
        if (isset($all['time_end']) && !empty($all['time_end'])) {
            $model = $model->where('created_at', '<=', $all['time_end']);
        }
        $res = $model ->orderBy('created_at', 'desc')->paginate($request->get('limit', 20))->toArray();
        foreach ($res['data'] as $key => $val) {
            $sum_bet_money=[];
            $sum_bet_money = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'),DB::raw('SUM(win_money-bet_money) as sum_money'))
                ->where('username', $val['username'])->first();
            if($sum_bet_money->sum_money)
            {
                $res['data'][$key]['sum_money'] =$sum_bet_money->sum_money;
            }else
            {
                $res['data'][$key]['sum_money'] =0;
            }
            if($sum_bet_money->bet_sum_money)
            {
                $res['data'][$key]['bet_sum_money'] =$sum_bet_money->bet_sum_money;
            }else
            {
                $res['data'][$key]['bet_sum_money'] =0;
            }
            //$res['data'][$key]['bet_sum_num'] =count($user_two_in);

            $charge_money=0;
            $charge_money=Orders::where('username', $val['username'])->where('oreder_type',1)->sum('amount');
            $res['data'][$key]['charge_money'] =$charge_money;
            $charge_num=0;
            $charge_num=Orders::where('username', $val['username'])->where('oreder_type',1)->count();
            $res['data'][$key]['charge_num'] =$charge_num;
            $withdrawal_money=0;
            $withdrawal_money=Withdrawal::where('username', $val['username'])->where('status',1)->sum('amount');
            $res['data'][$key]['withdrawal_money'] =$withdrawal_money;

            $withdrawal_num=0;
            $withdrawal_num=Withdrawal::where('username', $val['username'])->where('status',1)->count();
            $res['data'][$key]['withdrawal_num'] =$withdrawal_num;

            $activity_money=0;
            $activity_money=Orders::where('username', $val['username'])->where('oreder_type',2)->sum('amount');
            $res['data'][$key]['activity_money'] =$activity_money;
        }
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

}
