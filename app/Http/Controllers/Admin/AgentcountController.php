<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BetOrders;
use App\Models\Member;
use App\Models\Orders;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class   AgentcountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws Throwable
     */
    public function index()
    {
        return view('admin.agentcount.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {

        //BetOrders;
        $all = $request->all();
        $model = Member::query()->where('user_type', 2);
        if (!empty($all['username'])) {
            $model = $model->where('username', 'like', '%' . $request->get('username') . '%');
        }
        if (!empty($all['time_start'])) {
            $time_start = $all['time_start'];
        } else {
            $time_start = date('Y-m-d 00:00:00', time());
        }
        $res = $model->orderBy('created_at', 'desc')->paginate($request->get('limit', 20))->toArray();
        foreach ($res['data'] as $key => $val) {
            //一级，二，三级代理下面用户数据统计
            $user_one_in = [];
            $user_two_in = [];
            $user_third_in = [];
            $sum_bet_money = [];
            //一级下面二级用户投注数据

            $user_one_in = Member::select('username')->where('superior', '=', $val['username'])->get()->toArray();
            $sum_bet_money_one = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_money'))
                ->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->whereIn('username', $user_one_in)->first();
            $bet_sum_num_one = BetOrders::whereIn('username', $user_one_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->distinct('username')->count('username');
            //二级下面三级用户投注数据
            $user_two_in = Member::select('username')->wherein('superior', $user_one_in)->get()->toArray();
            $sum_bet_money_two = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_money'))
                ->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->whereIn('username', $user_two_in)->first();
            $bet_sum_num_two = BetOrders::whereIn('username', $user_two_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->distinct('username')->count('username');
            //三级下面用户投注数据
            $user_third_in = Member::select('username')->wherein('superior', $user_two_in)->get()->toArray();
            $sum_bet_money_third = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_money'))
                ->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->whereIn('username', $user_third_in)->first();
            $bet_sum_num_third = BetOrders::whereIn('username', $user_third_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->distinct('username')->count('username');

            $res = $this->getSumMoneyData($sum_bet_money_one, $sum_bet_money_two, $sum_bet_money_third, $res, $key, $bet_sum_num_one, $bet_sum_num_two, $bet_sum_num_third, $user_one_in, $time_start, $user_two_in, $user_third_in);
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
     * @param $sum_bet_money_one
     * @param $sum_bet_money_two
     * @param $sum_bet_money_third
     * @param array $res
     * @param $key
     * @param $bet_sum_num_one
     * @param $bet_sum_num_two
     * @param $bet_sum_num_third
     * @return array
     */
    public function getBetSumMoney($sum_bet_money_one, $sum_bet_money_two, $sum_bet_money_third, array $res, $key, $bet_sum_num_one, $bet_sum_num_two, $bet_sum_num_third): array
    {
        $bet_sum_money = $sum_bet_money_one->bet_sum_money + $sum_bet_money_two->bet_sum_money + $sum_bet_money_third->bet_sum_money;
        $res['data'][$key]['bet_sum_money'] = $bet_sum_money;

        $sum_money = $sum_bet_money_one->sum_money + $sum_bet_money_two->sum_money + $sum_bet_money_third->sum_money;
        $res['data'][$key]['sum_money'] = $sum_money;

        $res['data'][$key]['bet_sum_num'] = $bet_sum_num_one + $bet_sum_num_two + $bet_sum_num_third;
        return $res;
    }

    /**
     * Display a listing of the resource.
     *下级代理
     * @param $id
     * @return Response
     * @throws Throwable
     */
    public function viewDown($id)
    {
        $data = Member::findOrFail($id);
        return view('admin.agentcount.dwuser', compact('data'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function viewDownData(Request $request): JsonResponse
    {
        $all = $request->all();
        //$model = Member::query()->where('user_type',3);
        $model = Member::query()->where('superior', $all['username']);
        if (!empty($all['username_input'])) {
            $model = $model->where('username', 'like', '%' . $request->get('username_input') . '%');
        }
        if (!empty($all['time_start'])) {
            $time_start = $all['time_start'];
        } else {
            $time_start = date('Y-m-d 00:00:00', time());
        }
        $res = $model->orderBy('created_at', 'desc')->paginate($request->get('limit', 20))->toArray();
        foreach ($res['data'] as $key => $val) {
            //一级，二，三级代理下面用户数据统计
            $user_one_in = [];
            $user_two_in = [];
            $user_third_in = [];
            $sum_bet_money = [];
            //一级下面二级用户投注数据
            $user_one_in = Member::select('username')->where('superior', '=', $val['username'])->get()->toArray();
            $sum_bet_money_one = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_money'))
                ->whereIn('username', $user_one_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->first();
            $bet_sum_num_one = BetOrders::whereIn('username', $user_one_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->distinct('username')->count('username');
            //二级下面三级用户投注数据
            $user_two_in = Member::select('username')->wherein('superior', $user_one_in)->get()->toArray();
            $sum_bet_money_two = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_money'))
                ->whereIn('username', $user_two_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->first();
            $bet_sum_num_two = BetOrders::whereIn('username', $user_two_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->distinct('username')->count('username');
            //三级下面用户投注数据
            $user_third_in = Member::select('username')->wherein('superior', $user_two_in)->get()->toArray();
            $sum_bet_money_third = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_money'))
                ->whereIn('username', $user_third_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->first();
            $bet_sum_num_third = BetOrders::whereIn('username', $user_third_in)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->distinct('username')->count('username');

            $res = $this->getSumMoneyData($sum_bet_money_one, $sum_bet_money_two, $sum_bet_money_third, $res, $key, $bet_sum_num_one, $bet_sum_num_two, $bet_sum_num_third, $user_one_in, $time_start, $user_two_in, $user_third_in);

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
     * @param $sum_bet_money_one
     * @param $sum_bet_money_two
     * @param $sum_bet_money_third
     * @param $res
     * @param $key
     * @param $bet_sum_num_one
     * @param $bet_sum_num_two
     * @param $bet_sum_num_third
     * @param $user_one_in
     * @param $time_start
     * @param $user_two_in
     * @param $user_third_in
     * @return array
     */
    public function getSumMoneyData($sum_bet_money_one, $sum_bet_money_two, $sum_bet_money_third, $res, $key, $bet_sum_num_one, $bet_sum_num_two, $bet_sum_num_third, $user_one_in, $time_start, $user_two_in, $user_third_in): array
    {
        $res = $this->getBetSumMoney($sum_bet_money_one, $sum_bet_money_two, $sum_bet_money_third, $res, $key, $bet_sum_num_one, $bet_sum_num_two, $bet_sum_num_third);

        //用户充值数据
        $charge_money_one = Orders::whereIn('username', $user_one_in)->where('oreder_type', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $charge_money_two = Orders::whereIn('username', $user_two_in)->where('oreder_type', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $charge_money_third = Orders::whereIn('username', $user_third_in)->where('oreder_type', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $res['data'][$key]['charge_money'] = $charge_money_one + $charge_money_two + $charge_money_third;
        //充值次数
        $charge_num_one = Orders::whereIn('username', $user_one_in)->where('oreder_type', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->count();
        $charge_num_two = Orders::whereIn('username', $user_two_in)->where('oreder_type', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->count();
        $charge_num_third = Orders::whereIn('username', $user_third_in)->where('oreder_type', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->count();
        $res['data'][$key]['charge_num'] = $charge_num_one + $charge_num_two + $charge_num_third;
        //用户提现数据
        $withdrawal_money_one = Withdrawal::whereIn('username', $user_one_in)->where('status', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $withdrawal_money_two = Withdrawal::whereIn('username', $user_two_in)->where('status', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $withdrawal_money_third = Withdrawal::whereIn('username', $user_third_in)->where('status', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $res['data'][$key]['withdrawal_money'] = $withdrawal_money_one + $withdrawal_money_two + $withdrawal_money_third;
        //提现次数
        $withdrawal_num_one = Withdrawal::whereIn('username', $user_one_in)->where('status', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->count();
        $withdrawal_num_two = Withdrawal::whereIn('username', $user_two_in)->where('status', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->count();
        $withdrawal_num_third = Withdrawal::whereIn('username', $user_third_in)->where('status', 1)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->count();
        $res['data'][$key]['withdrawal_num'] = $withdrawal_num_one + $withdrawal_num_two + $withdrawal_num_third;
        //活动费用
        $activity_money_one = Orders::whereIn('username', $user_one_in)->where('oreder_type', 2)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $activity_money_two = Orders::whereIn('username', $user_two_in)->where('oreder_type', 2)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $activity_money_third = Orders::whereIn('username', $user_third_in)->where('oreder_type', 2)->whereBetween('created_at', [$time_start, date('Y-m-d H:i:s', time())])->sum('amount');
        $res['data'][$key]['activity_money'] = $activity_money_one + $activity_money_two + $activity_money_third;
        return $res;
    }
}
