<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\BetOrders;
use App\Models\Agent;
use App\Models\OperationLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
     * 后台布局
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function layout()
    {
        return view('admin.layout');
    }

    private function mysqlversion()
    {
        $mysqlv = DB::select('SELECT VERSION() as VERSION;');
        return $mysqlv[0]->VERSION;
    }

    /**
     * 后台首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //DB::connection()->enableQueryLog();
        $today_sum_bet = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_win_money'))
            ->where('created_at', '>=', date('Y-m-d 00:00:00'))->first();

        $sum_bet = BetOrders::select(DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_win_money'))->first();
        $data = [
            //总用户
            'user_count' => Member::count(),
            //总代理人数
            'user_agent_count' => Member::wherein('user_type', [2, 3, 4])->count(),
            //今日登录人数
            'user_active_count' => Member::where('logintime', '>=', strtotime(date('Y-m-d 00:00:00')))->count(),
            //今日充值人数
            'user_charge_count' => DB::table('orders')->where('created_at', '>=', date('Y-m-d', time()))->where('oreder_type', 1)->orderBy('user_id')->count(),
            //今日新增用户
            'user_newadd_count' => Member::where('created_at', '>=', date('Y-m-d 00:00:00'))->count(),
            //今日提现人数
            'user_withdrawal_count' => 0,
            //今日充值金额
            'user_charge_sum' => DB::table('orders')
                ->where('created_at', '>=', date('Y-m-d', time()))->where('oreder_type', 1)->sum('amount'),

        //今日投注金额
        'today_bet_sum_money' => $today_sum_bet['bet_sum_money'],
            //今日盈亏金额
            'today_sum_win_money' =>$today_sum_bet['sum_win_money'],
            //今日中奖金额
            'today_win_money_sum' => BetOrders::where('created_at', '>=', date('Y-m-d 00:00:00'))->sum('win_money'),
            //今日新用户-成功总订单
            /*'newadd_pay_count' => Member::leftjoin('orders', 'orders.uid', '=', 'member.id')
                ->where('member.created_at', '>=', date('Y-m-d 00:00:00'))->where('orders.pay_status', 2)->count(),*/
            //总投注金额
            'bet_sum_money' => $sum_bet['bet_sum_money'],
            //总盈亏金额
            'sum_win_money' =>$sum_bet['sum_win_money'],
           //总中奖金额
            'win_money_sum' => BetOrders::sum('win_money'),
            //总余额
            'user_money_sum' => Member::sum('money'),

            /*'widget_config' => [
                ['name'=>'IP地址','text'=> request()->server('SERVER_ADDR')],//gethostbyname($_SERVER["SERVER_NAME"])
                ['name'=>'Web服务','text'=> request()->server('SERVER_SOFTWARE')],
                ['name'=>'Laravel','text'=> app()->version()],
                ['name'=>'PHP版本','text'=> phpversion()],
                ['name'=>'Mysql版本','text'=> $this->mysqlversion()],
            ]*/
        ];
        /*$logs = DB::getQueryLog();
        echo json_encode($logs);*/
        return view('admin.index.index', compact('data'));
    }

    /**后台收入统计图表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function line_chart(Request $request)
    {
        /*$data_account = DB::select("SELECT FORMAT(SUM(amount),2) AS account, DATE_FORMAT(pay_time, '%Y-%m-%d' ) AS cdate
        FROM levo_orders WHERE pay_time<CURDATE( )+1 AND pay_time >=DATE_SUB(CURDATE( ),
        INTERVAL 10 DAY) and   pay_status=2 GROUP BY DATE_FORMAT(pay_time,'%Y-%m-%d')");*/

        $sql = "SELECT  SUM(win_money-bet_money)  AS account,DATE_FORMAT( created_at, '%Y-%m-%d' ) AS cdate
        FROM `levo_bet_orders` WHERE created_at < CURDATE( ) + 1 AND created_at >= DATE_SUB( CURDATE( ), INTERVAL 10 DAY )
        GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d')";
        $data_account = DB::select($sql);

        $sql = "SELECT  SUM(bet_money)  AS bet_money,DATE_FORMAT( created_at, '%Y-%m-%d' ) AS cdate
        FROM `levo_bet_orders` WHERE created_at < CURDATE( ) + 1 AND created_at >= DATE_SUB( CURDATE( ), INTERVAL 10 DAY )
        GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d')";
        $data_bet_money = DB::select($sql);

        $sql = "SELECT SUM(win_money) AS win_money,DATE_FORMAT( created_at, '%Y-%m-%d' ) AS cdate
        FROM `levo_bet_orders`
        WHERE created_at < CURDATE( ) + 1 AND created_at >= DATE_SUB( CURDATE( ), INTERVAL 10 DAY )
        GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d')";
        $data_win_money = DB::select($sql);
        $data_time = array_column($data_account, 'cdate');
        $data_account = array_column($data_account, 'account');
        $data_bet_money = array_column($data_bet_money, 'bet_money');
        $data_win_money = array_column($data_win_money, 'win_money');
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data_time' => $data_time,
            'data_account' => $data_account,
            'data_bet_money' => $data_bet_money,
            'data_win_money' => $data_win_money,
        ];
        return response()->json($data);
    }


    /**后台设备统计图表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pie_chart(Request $request)
    {
        $all_android_count = Member::where('device_type', 1)->count();
        $all_ios_count = Member::where('device_type', 2)->count();
        $today_android_count = Member::where('created_at', '>=', date('Y-m-d 00:00:00'))->where('device_type', 1)->count();
        $today_ios_count = Member::where('created_at', '>=', date('Y-m-d 00:00:00'))->where('device_type', 2)->count();
        //今日日盈亏比
        $today_pay_android_count = BetOrders::where('is_win', 1)->where('created_at', '>=', date('Y-m-d 00:00:00'))->count();
        $today_pay_ios_count = BetOrders::where('is_win', 0)->where('created_at', '>=', date('Y-m-d 00:00:00'))->count();
        $today_pay_android_count = $today_pay_android_count ? $today_pay_android_count : 1;
        $today_pay_ios_count = $today_pay_ios_count ? $today_pay_ios_count : 1;

        $today_android_count = $today_android_count ? $today_android_count : 1;
        $today_ios_count = $today_ios_count ? $today_ios_count : 1;
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'all_android_count' => $all_android_count,
            'all_ios_count' => $all_ios_count,
            'today_android_count' => $today_android_count,
            'today_ios_count' => $today_ios_count,
            'today_pay_android_count' => $today_pay_android_count,
            'today_pay_ios_count' => $today_pay_ios_count,

        ];
        return response()->json($data);
    }


    /**代理用户饼状统计图
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function agent_pie_chart(Request $request)
    {
        $user = auth()->user();
        $all_android_count = Member::where('admin_id', $user->id)->where('device_type', 1)->count();
        $all_ios_count = Member::where('admin_id', $user->id)->where('device_type', 2)->count();
        $today_android_count = Member::where('admin_id', $user->id)->where('created_at', '>=', date('Y-m-d 00:00:00'))->where('device_type', 1)->count();
        $today_ios_count = Member::where('admin_id', $user->id)->where('created_at', '>=', date('Y-m-d 00:00:00'))->where('device_type', 2)->count();
        $today_pay_android_count = Member::leftjoin('orders', 'orders.uid', '=', 'member.id')
            ->where('member.admin_id', $user->id)->where('orders.pay_time', '>=', date('Y-m-d 00:00:00'))->where('orders.pay_status', 2)->where('member.device_type', 1)->count();
        $today_pay_ios_count = Member::leftjoin('orders', 'orders.uid', '=', 'member.id')
            ->where('member.admin_id', $user->id)->where('orders.pay_time', '>=', date('Y-m-d 00:00:00'))->where('orders.pay_status', 2)->where('member.device_type', 2)->count();
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'all_android_count' => $all_android_count,
            'all_ios_count' => $all_ios_count,
            'today_android_count' => $today_android_count,
            'today_ios_count' => $today_ios_count,
            'today_pay_android_count' => $today_pay_android_count,
            'today_pay_ios_count' => $today_pay_ios_count,

        ];
        return response()->json($data);
    }

    /**
     * 后台代理首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function agent_index(Request $request)
    {
        $user = auth()->user();
        //DB::connection()->enableQueryLog();
        $yesderday_time = date('Y-m-d 00:00:00', strtotime("-1days", strtotime(date('Y-m-d 00:00:00'))));
        $today_time = date('Y-m-d 00:00:00');
        $data = [
            //总用户
            'user_count' => Member::where('admin_id', $user->id)->count(),
            //今日活跃用户
            'user_active_count' => Member::where('login_date', '>=', date('Y-m-d 00:00:00'))->where('admin_id', $user->id)->count(),
            //今日新增用户
            'user_newadd_count' => Member::where('created_at', '>=', date('Y-m-d 00:00:00'))->where('admin_id', $user->id)->count(),
            //今日代理实际总收入
            'real_agent_account_count' => Agent::where('created_at', '>=', date('Y-m-d 00:00:00'))->where('admin_id', $user->id)->sum('agent_real_income'),
            //昨日代理实际收入
            'yesterday_real_agent_account_count' => Agent::where('created_at', '>=', $yesderday_time)->where('created_at', '<', $today_time)->where('admin_id', $user->id)->sum('agent_real_income'),
            //今日新用户-成功总订单
            'newadd_pay_count' => Member::leftjoin('orders', 'orders.uid', '=', 'member.id')
                ->where('member.created_at', '>=', date('Y-m-d 00:00:00'))
                ->where('orders.admin_id', $user->id)->where('orders.pay_status', 2)
                ->where('orders.is_deduct', 0)
                ->where('member.admin_id', $user->id)->count(),
            //今日vip订单
            'vip_order_count' => Order::where('pay_time', '>=', date('Y-m-d 00:00:00'))
                ->where('is_deduct', 0)
                ->where('pay_status', 2)->where('product_type', 1)
                ->where('admin_id', $user->id)->count(),
            //今日金币订单
            'gold_order_count' => Order::where('pay_time', '>=', date('Y-m-d 00:00:00'))
                ->where('pay_status', 2)->where('product_type', 2)->where('is_deduct', 0)
                ->where('admin_id', $user->id)->count(),

        ];
        /*$logs = DB::getQueryLog();
       echo json_encode($logs);*/
        return view('admin.index.agent_index', compact('data'));
    }

    /**
     * 后台代理图表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function agent_line_chart(Request $request)
    {
        $user = auth()->user();
        $sql = " SELECT SUM(agent_real_income) AS agent_account,DATE_FORMAT( created_at, '%Y-%m-%d' ) AS cdate
 FROM `levo_agent_paytotal` WHERE created_at < CURDATE( ) + 1 AND created_at >= DATE_SUB( CURDATE( ), INTERVAL 10 DAY )
 and admin_id={$user->id} GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d') ";
        $data_agent = DB::select($sql);
        $data_time = array_column($data_agent, 'cdate');
        $data_agent = array_column($data_agent, 'agent_account');
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data_time' => $data_time,
            'data_agent' => $data_agent,
        ];
        return response()->json($data);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 数据表格接口
     */
    public function data(Request $request)
    {
        $model = $request->get('model');
        switch (strtolower($model)) {
            case 'user':
                $query = new AdminUser();
                $query = $query->where('user_type', 0);
                break;
            case 'role':
                $query = new Role();
                break;
            case 'permission':
                $query = new Permission();
                $query = $query->where('parent_id', $request->get('parent_id', 0));
                break;
            case 'member':
                $query = new Member();
                break;
            default:
                $query = new AdminUser();
                break;
        }
        $res = $query->paginate($request->get('limit', 20))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * test
     */
    public function test(Request $request)
    {
        $file = $_FILES['ads_pic'];
        echo json_encode(upload_file($file));
    }


}
