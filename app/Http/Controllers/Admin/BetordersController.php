<?php

namespace App\Http\Controllers\Admin;

use App\Models\BetOrders;
use App\Models\Member;
use App\Models\UserAccountChange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BetordersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.betorders.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $all = $request->all();
        $model = BetOrders::query();
        if (!empty($all['order'])) {
            $model = $model->where('order', 'like', '%' . $request->get('order') . '%');
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
        if (isset($all['issue']) && !empty($all['issue'])) {
            $model = $model->where('issue', '=', $all['issue']);
        }

        if (isset($all['open_status']) && !empty($all['open_status'])) {
            if($all['open_status']==1)
            {
                  $model = $model->where('is_win', 0);
            }
            if($all['open_status']==2)
            {
                $model = $model->where('is_win', 1);
            }
            if($all['open_status']==3)
            {
                $model = $model->where('is_open', 0);
            }
            if($all['open_status']==4)
            {
                $model = $model->where('is_open', 1);
            }
            if($all['open_status']==5)
            {
                $model = $model->where('is_cancel', 0);
            }
            if($all['open_status']==6)
            {
                $model = $model->where('is_cancel', 1);
            }
        }

        $res = $model->orderBy('id', 'desc')->paginate($request->get('limit', 15))->toArray();
        foreach ( $res['data'] as $key => $value) {
            if($value['is_win']==0)
            {
                if($value['personal_profit_and_loss']!=0)
                {
                    $res['data'][$key]['personal_profit_and_loss']='-'.$value['personal_profit_and_loss'];
                }
            }
            else
            {
                if($value['personal_profit_and_loss']!=0) {
                    $res['data'][$key]['personal_profit_and_loss'] = '+' . $value['personal_profit_and_loss'];
                }
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
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = BetOrders::findOrFail($id);
        //print_r($data);
        return view('admin.betorders.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $news = BetOrders::findOrFail($id);
        $data = $request->except('字段');
        $data['bet_money']=$data['single_money'];
        if ($news->update($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '更新信息成功'
                ]);
            } else {
                return redirect()->to(route('admin.betorders'))->with(['status' => '更新信息成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.betorders'))->withErrors('系统错误');
        }
    }

    /**
     * Update the specified resource in storage.
     *撤单
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function upuser(Request $request)
    {
        $id = $request->get('ids');
        $order = BetOrders::findOrFail($id)->first();
        if ($order) {
            $user_data = Member::where('username', '=', $order['username'])->first();
            //更新注单表
            $in_order_data['is_cancel'] = 0;
            $in_order_data['win_money'] = 0;
            $in_order_data['win_rebate'] = 0;
            $in_order_data['personal_profit_and_loss'] = 0;
            BetOrders::where('id', '=', $id)
                ->update($in_order_data);
           //更新用户余额
            $in_user_data['money'] = $user_data['money'] + $order['bet_money'];
            Member::where('username', '=', $order['username'])
                ->update($in_user_data);
            //插入账变表
            $adminname = auth()->user()->username;
            $in_account_data=[
                'username'=>$order['username'],
                'actual_name'=>$user_data['actual_name'],
                'type'=>11,
                'games'=>$order['colorful'],
                'play'=>$order['play'],
                'issue'=>$order['issue'],
                'bet'=>$order['bet'],
                'bet_money'=> -$order['bet_money'],
                'money'=> $in_user_data['money'],
                'operator'=>$adminname,
                'remark'=>'撤销注单',
                'order_num'=>$order['order'],
            ];
            UserAccountChange::create($in_account_data);
            return response()->json(['code' => '0', 'status' => '撤销订单成功']);
        } else {
            return response()->json(['code' => 1, 'msg' => '撤销订单失败']);
        }
    }

}
