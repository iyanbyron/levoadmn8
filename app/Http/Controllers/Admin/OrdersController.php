<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideosmallclassCreateRequest;
use App\Http\Requests\VideosmallclassUpdateRequest;
use App\Models\Bank;
use App\Models\Member;
use App\Models\News;
use App\Models\Orders;
use App\Models\UserAccountChange;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws \Throwable
     */
    public function index()
    {
        return view('admin.orders.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $all = $request->all();
        $model = Orders::query()->select('member.actual_name', 'orders.*');
        $model = $model->leftjoin('member', 'member.id', '=', 'orders.user_id');
        if (!empty($all['username'])) {
            $model = $model->where('orders.username', '=', $all['username']);
        }
        if (isset($all['actual_name']) && !empty($all['actual_name'])) {
            $model = $model->where('member.actual_name', '=', $all['actual_name']);
        }
        if (!empty($all['time_start'])) {
            $model = $model->where('orders.created_at', '>=', $all['time_start']);
        }
        if (isset($all['time_end']) && !empty($all['time_end'])) {
            $model = $model->where('orders.created_at', '<=', $all['time_end']);
        }
        $res = $model->orderBy('orders.id', 'desc')->paginate($request->get('limit', 15))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $user_data = Member::where('username', '=', $data['username'])->first();
        if (!$user_data) {
            return response()->json([
                'status' => 'fail',
                'message' => '用户不存在'
            ]);
        }
        mt_srand((double )microtime() * 1000000);
        $order_num = date("YmdHis") . str_pad(mt_rand(1, 99999), 5, "0", 0);
        $data['user_id'] = $user_data->id;
        $admin_name = auth()->user()->username;
        $admin_id = auth()->user()->id;
        $data['admin_id'] = $admin_id;
        $data['admin_name'] = $admin_name;
        $in_user_data['money'] = $user_data['money'] + $data['amount'];
        $in_user_data['code_amount'] = $data['amount'] * 100 + $user_data['code_amount'];
        $data['after_money'] = $in_user_data['money'];
        $data['order_num'] = $order_num;
        if ($data['amount'] > 0) {
            Member::where('id', $user_data->id)->increment('recharge_today', $data['amount']);
            Member::where('id', $user_data->id)->increment('histor_recharge', $data['amount']);
            $date_time = date('Y-m-d H:i:s');
            $data_news = [
                'title' => "收到充值金额为{$data['amount']}元",
                'content' => "于{$date_time}收到充值金额为 {$data['amount']}元,如界面无金额变动信息 请手动刷新余额",
                'user_id' => $user_data->id,
                'username' => $user_data->username,
                'admin_name' => $admin_name,
                'is_reply' => 0,
            ];
            News::create($data_news);
        }

        if (Orders::create($data)) {
            if (request()->ajax()) {
                //更新用户余额
                Member::where('username', '=', $data['username'])
                    ->update($in_user_data);
                //插入账变表
                $in_account_data = [

                    'username' => $user_data['username'],
                    'actual_name' => $user_data['actual_name'],
                    'type' => 1,
                    //'games'=>'',
                    //'play'=>'',
                    //'issue'=>'',
                    //'bet'=>'',
                    'bet_money' => $data['amount'],
                    'money' => $in_user_data['money'],
                    'operator' => $admin_name,
                    'remark' => '后台手动加钱,扣钱成功',
                    'order_num' => $order_num,
                ];
                UserAccountChange::create($in_account_data);
                return response()->json([
                    'status' => 'success',
                    'message' => '加钱/扣钱成功'
                ]);
            } else {
                return redirect()->to(route('admin.orders'))->with(['status' => '加钱/扣钱成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.orders'))->withErrors('系统错误');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data = ['bank_id' => '0'];
        $data = (object)$data;
        $bank_list = Bank::select('id', 'bank_name')->get();
        return view('admin.orders.create', compact('data', 'bank_list'));
    }

    /**
     * 获取用户信息
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getuser(Request $request)
    {
        $username = $request->input('username', '');
        $user_data = Member::where('username', '=', $username)->first();
        if (!$user_data) {
            return response()->json([
                'status' => 'fail',
                'msg' => '用户不存在'
            ]);
        } else {
            if ($user_data->user_type == 1) {
                $is_agent = "会员";
            }
            if ($user_data->user_type == 2) {
                $is_agent = "总代理";
            }
            if ($user_data->user_type == 3) {
                $is_agent = "一级代理";
            }
            if ($user_data->user_type == 4) {
                $is_agent = "二级代理";
            }
            return response()->json([
                'status' => 'success',
                'msg' => '获取成功',
                'data' => [
                    'actual_name' => $user_data->actual_name,
                    'money' => $user_data->money,
                    'is_agent' => $is_agent,
                ]

            ]);

        }

    }
}
