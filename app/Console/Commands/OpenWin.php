<?php

namespace App\Console\Commands;

use App\Models\UserAccountChange;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpenWin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OpenWin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '3分钟自动开奖';

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
        $aa = strtotime(date('H:i:s', time()));
        $aaa = strtotime(date('00:00:00', time()));
        $b = $aa - $aaa;
        $sort = intval($b / 180 + 1);
        if ($sort < 1) {
            $sort = 480;
        } elseif ($sort < 10) {
            $sort = "00" . $sort;
        } elseif ($sort < 100) {
            $sort = "0" . $sort;
        }	
        $issueNow = intval(date('Ymd', time()).$sort);
	$openTime = DB::table('lottery')->where([['issue', '=', $issueNow]])->first();
        //3分钟开一期奖
        DB::table('lottery')->where([['issue', '=', $issueNow], ['is_open', '=', 0]])
            ->update(['updated_at' => date('Y-m-d H:i:s', time()), 'open_time' => date('Y-m-d H:i:s', time()), 'is_open' => 1]);

        DB::table('lottery')->where([['issue', '=', $issueNow], ['is_open', '=', 1]])->orderBy('open_time', 'DESC')->get();

        for ($i = 1; $i <= 7; $i++) {
            //拿到和值 中奖号码
            $lotteryList = DB::table('lottery')
                ->where([['is_open', '=', 1], ['game_id', '=', $i]])->orderBy('updated_at', 'DESC')->first();

            $lotteryList->sum_value = substr($lotteryList->win_number, 0, 1) + substr($lotteryList->win_number, 1, 1) + substr($lotteryList->win_number, 2, 1);
            //获取注单
            $betOrders = DB::table('bet_orders')
                ->select('bet', 'odds', 'single_money', 'user_id', 'order')
                ->where([['game_id', '=', $i],
                    ['issue', '=', $lotteryList->issue],
                    ['is_open', '=', 0],
			['created_at', '<=', $openTime->open_time]
		])->get();

            if (empty($betOrders)) {
            		return false;
		}
            //和值对应的福禄寿喜 game_type
            $openWinNum = DB::table('num')
                ->select('game_type')
                ->where('value', $lotteryList->sum_value)->first();
            if (empty($openWinNum)) {
                return false;
            }
            //判断存在 即表示中奖
            if(!is_array($betOrders->toArray())){
                return false;
            }

            foreach ($betOrders->toArray() as $k => $v) {
                //中奖号码
                $openWinNums = explode(',', $openWinNum->game_type);
                Log::info($v->bet);
                Log::info($openWinNums);
                //判断是否中奖   $v->bet 下注号码
                if(in_array($v->bet,$openWinNums)){
                    //中奖金额
                    $data['win_money'] = $v->odds * $v->single_money;
                    //个人盈亏 赢
                    //更新 开奖 赢
        	$res = ['personal_profit_and_loss' =>$data['win_money'] - $v->single_money,'win_numbers' => $lotteryList->win_number, 'win_money' => $data['win_money'],'is_win' => 1, 'is_open' => 1, 'updated_at' => date('Y-m-d H:i:s', time())];
                    Log::info($res);        
	    DB::table('bet_orders')->where([
                  	['order','=',$v->order]
			  ])->update($res);
			Log::info($v->order);
			  $memberList = DB::table('member')->where('id', $v->user_id)->first();
                    $newMoney = $memberList->money + $data['win_money'];
                    DB::table('member')->where('id', $v->user_id)
                        ->update(['money' => $newMoney, 'updated_at' => date('Y-m-d H:i:s', time())]);
                    $userAccountChange = DB::table('user_account_change')->where('order_num', $v->order)->first();
                    $order = new UserAccountChange();

                    $order->username = $userAccountChange->username;
                    $order->actual_name = $userAccountChange->actual_name;
                    $order->type = 4;
                    $order->game_name = $userAccountChange->game_name;
                    $order->play = $userAccountChange->play;
                    $order->issue = $userAccountChange->issue;
                    $order->order_num = $userAccountChange->order_num;
                    $order->bet = $userAccountChange->bet;
                    $order->bet_money = $data['win_money'];
                    $order->is_win = 1;
                    $order->money = $newMoney;
			$order->updated_at = date('Y-m-d H:i:s', time());
                    $order->save();
                } else {
                    //更新 开奖 赢
        	   DB::table('bet_orders')->where([
                        ['game_id', '=', $i],
                        ['issue', '=', $lotteryList->issue],
			['order','=',$v->order]
                    ])->update(['win_money' => 0, 'personal_profit_and_loss' => -$v->single_money, 'win_numbers' => $lotteryList->win_number, 'is_win' => 0, 'is_open' => 1, 'updated_at' => date('Y-m-d H:i:s', time())]);
                    $memberList = DB::table('member')->where('id', $v->user_id)->first();
                    $userAccountChange = DB::table('user_account_change')->where('order_num', $v->order)->first();
                    $order = new UserAccountChange();

                    $order->username = $userAccountChange->username;
                    $order->actual_name = $userAccountChange->actual_name;
                    $order->type = 4;
                    $order->game_name = $userAccountChange->game_name;
                    $order->play = $userAccountChange->play;
                    $order->issue = $userAccountChange->issue;
                    $order->order_num = $userAccountChange->order_num;
                    $order->bet = $userAccountChange->bet;
                    $order->bet_money = 0;
                    $order->is_win = 0;
                    $order->money = $memberList->money;
     				$order->updated_at = date('Y-m-d H:i:s', time());
                    $order->save();
	           }
            }
        }
        return date('Y-m-d H:i:s', time());
    }
}

