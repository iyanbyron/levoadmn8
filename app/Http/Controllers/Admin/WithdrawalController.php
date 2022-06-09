<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\News;
use App\Models\UserAccountChange;
use App\Models\Withdrawal;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Swoft\Http\Message\Response;
use Throwable;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     * @throws Throwable
     */
    public function index()
    {
        return view('admin.withdrawal.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $model = withdrawal::select('*');
        if (!empty($request->get('username'))) {
            $model = $model->where('username', 'like', $request->get('username') . '%');
        }

        //if ($request->get('is_status')!="" ) {
        if (!empty($request->get('is_status')) || $request->get('is_status') != "") {
            $model = $model->where('status', '=', $request->get('is_status'));
        }
        if (!empty($request->get('time_start'))) {
            $yestoday_time = date("Y-m-d 00:00:00", strtotime("+1 day", strtotime($request->get('time_start'))));
            $model = $model->where('updated_at', '>=', $request->get('time_start'));
            $model = $model->where('updated_at', '<', $yestoday_time);
            $model = $model->where('updated_at', '>=', $request->get('time_start'));
        }
        $model = $model->orderBy('id', 'desc')->paginate($request->get('limit', 20))->toArray();

        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $model['total'],
            'data' => $model['data']
        ];
        return response()->json($data);
    }

    /**
     * 同意打款成功
     * @param Request $request
     * @return JsonResponse
     */
    public function agreepay(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $admin_name = auth()->user()->name;
        if (empty($id)) {
            return response()->json(['code' => 1, 'msg' => '请选择记录项']);
        }

        $Withdraws = withdrawal::where('id', $id)->first();
        if ($Withdraws->status != 0) {
            return response()->json(['code' => 1, 'msg' => '当前记录已操作,请刷新页面']);
        }
        Member::where('id', '=', $Withdraws->user_id)->update(['frozen_money' => 0]);
        $date_time = date('Y-m-d H:i:s');
        $amount = $Withdraws->amount;
        $data_news = [
            'title' => "兑换金额为{$amount}元",
            'content' => "于{$date_time}兑换金额为{$amount}元，详情请查看兑换记录",
            'user_id' => $Withdraws->user_id,
            'username' => $Withdraws->username,
            'admin_name' => $admin_name,
            'is_reply' => 0,
        ];
        News::create($data_news);
        //  $in_account_data = [
        //    'type' => 8,
        //  'operator' => $admin_name,
        //  'remark' => '提现成功'
        //  ];
        $a = DB::table('user_account_change')
            ->where('order_num', $Withdraws->withdraw_order)
            ->update(['type' => 8, 'operator' => $admin_name, 'remark' => '提现成功']);
        //   UserAccountChange::where('order_num', $Withdraws->order_num)->update($in_account_data);
        $data['status'] = 1;
        $data['finish_time'] = date('Y-m-d H:i:s');
        $data['operator'] = $admin_name;
        $data['remark'] = '提现成功';
        if ($Withdraws->where('id', $id)->update($data)) {
            return response()->json(['code' => 0, 'msg' => '操作成功']);
        }
        return response()->json(['code' => 1, 'msg' => '操作失败']);

    }

    /**
     * 拒绝打款，出款失败
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $admin_name = auth()->user()->name;
        $id = $request->get('id');
        if (empty($id)) {
            return response()->json(['code' => 1, 'msg' => '请选择记录项']);
        }
        $withdrawal = withdrawal::where('id', $id)->first();
        if ($withdrawal->status != 0) {
            return response()->json([
                'status' => 'fail',
                'message' => '当前记录已操作,请刷新页面'
            ]);
        }
        $user_data = Member::where('username', '=', $withdrawal->username)->first();
        $data['status'] = 2;
        $data['finish_time'] = date('Y-m-d H:i:s');
        $data['operator'] = $admin_name;
        $data['remark'] = $request->remark;
        if ($withdrawal->where('id', $id)->update($data)) {
            //更新用户余额
            $in_user_data = [
                'money' => $user_data['money'] + $withdrawal->amount,
                'withdraw_today' => $user_data['withdraw_today'] - $withdrawal->amount,
                'histor_withdraw' => $user_data['histor_withdraw'] - $withdrawal->amount,
                'frozen_money' => 0
            ];
            Member::where('username', $user_data->username)->update($in_user_data);
            $date_time = date('Y-m-d H:i:s');
            $amount = $withdrawal->amount;
            $data_news = [
                'title' => "兑换金额为{$amount}元",
                'content' => "于{$date_time}兑换金额为{$amount}元,兑换失败,详情请查看系统消息",
                'user_id' => $withdrawal->user_id,
                'username' => $withdrawal->username,
                'admin_name' => $admin_name,
                'is_reply' => 0,
            ];
            News::create($data_news);
            $in_account_data = [
                'actual_name' => $user_data['actual_name'],
                'type' => 7,
                'bet_money' => $withdrawal->amount,
                'money' => $in_user_data['money'],
                'operator' => $admin_name,
                'remark' => '后台提现拒绝返回余额'
            ];
            UserAccountChange::where('order_num', $withdrawal->withdraw_order)->update($in_account_data);
            return response()->json(['status' => 'success', 'message' => '操作成功']);
        } else {
            return response()->json(['code' => 1, 'msg' => '操作失败']);
        }
    }

    /**
     * @param $id
     * @return array|false|Application|Factory|View|mixed|Response
     * @throws Throwable
     */
    public function edit($id)
    {
        $data = withdrawal::findOrFail($id);
        return view('admin.withdrawal.edit', compact('data'));
    }
}
