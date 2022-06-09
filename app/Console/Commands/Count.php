<?php

namespace App\Console\Commands;

use App\Models\Orders;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Count extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '平台日报';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //当前日期
        $data['date_time'] = date("Y-m-d", time());
        //余额
        $data['money'] = DB::table('member')->sum('money');
        //中奖金额
        $win_numbers = DB::table('bet_orders')->where('updated_at', '>=', date('Y-m-d', time()))->where(['is_win' => 1, 'is_open' => 1, 'is_cancel' => 1])->sum('win_money');
        //下注金额
        $single_money = DB::table('bet_orders')->where('created_at', '>=', date('Y-m-d', time()))->where(['is_win' => 1, 'is_open' => 1, 'is_cancel' => 1])->sum('single_money');
        //总盈亏
        $data['profit_and_loss'] = $win_numbers - $single_money;
        //充值
        $data['recharge'] = DB::table('orders')
            ->where('created_at', '>=', date('Y-m-d', time()))->where('oreder_type', 1)->sum('amount');
        //人数
        $data['num_peo'] = DB::table('orders')->where('created_at', '>=', date('Y-m-d', time()))->where('oreder_type', 1)->orderBy('user_id')->count();
        //笔数
        $data['pen_count'] = DB::table('orders')->where('created_at', '>=', date('Y-m-d', time()))->where('oreder_type', 1)->count();
        //首充
        //$data['first_charge']= DB::table('orders')->where(['created_at' => date('Y-m-d', time()), 'oreder_type' => 1])->orderBy('user_id')->count();
//dd($data['first_charge']);
        //已充值过的用户
        //$first_charge_data=DB::select("SELECT  distinct username  FROM  levo_orders");
        $first_charge_data = Orders::select('user_id')->distinct()->where('created_at', '<', date('Y-m-d', time()))->get();
        if ($first_charge_data) {
            $first_charge_data = $first_charge_data->toArray();
        } else {
            $first_charge_data = [0, 0];
        }
        $data['first_charge'] = DB::table('orders')->whereNotIn('user_id', $first_charge_data)->where('created_at', '>=', date('Y-m-d', time()))->where('oreder_type', 1)->count();
        //提现
        $data['withdraw'] = DB::table('withdrawal')->where('updated_at', '>=', date('Y-m-d', time()))->where('status', 1)->sum('amount');
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $count = DB::table('count')->where('date_time', '=', $data['date_time'])->get()->toArray();
        if (!empty($count)) {
            DB::table('count')->where('date_time', '=', $data['date_time'])->update($data);
        } else {
            DB::table('count')->insert($data);
        }
    }
}

