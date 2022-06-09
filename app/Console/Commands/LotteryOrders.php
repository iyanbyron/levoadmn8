<?php

namespace App\Console\Commands;

use App\Models\Lottery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LotteryOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LotteryOrders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '0点定时 所有开奖结果';

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
     * @return void
     */
    public function handle()
    {
        //0点
        $start = strtotime(date("Y-m-d 00:03:00"));
        //24点
        $end = strtotime(date('Y-m-d 00:00:00', strtotime("+1 day")));

        //7种彩种
        for ($i = 1; $i <= 7; $i++) {
            //3分钟一期
            for ($time = $start; $time <= $end; $time = $time + 3 * 60) {
                //期号后三位数字0填充
                $sort = ($time - $start) / (3 * 60) + 1;
                if ($sort < 10) {
                    $sort = "00" . $sort;
                } elseif ($sort < 100) {
                    $sort = "0" . $sort;
                }

                //每期开奖时间
                $open_time = date("Y-m-d H:i:s", $time);

                //随机三位号码
                $str = '123456';
                //打乱字符串
                $randStr = str_shuffle($str);
                //substr(string,start,length);返回字符串的一部分
                $win_number = substr($randStr, 0, 3);

                $ret = [
                    'sort' => intval($sort),
                    'open_time' => $open_time,
                    'game_id' => $i,
                    'issue' => intval(date("Ymd") . $sort),
                    'win_number' => $win_number,
                ];
                $lottery = new Lottery();
                //往数据库批量插入数据
                $result = $lottery::create($ret);
                if (!$result) {
                    DB::rollBack();
                }
            }
        }
    }
}
