<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BetOrders;
use App\Models\Games;
use App\Models\Lottery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LotteryController extends Controller
{
    /**
     * 定义jwt验证$guard参数
     * @var string
     */
    protected $guard = 'api';

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['lotterydraw', 'lotterydrawhis', 'gamesetting', 'lotterys', 'income']]);
    }

    /**
     * @param Request $request
     * 查询某彩种某天开奖结果
     * @return JsonResponse
     */
    public function lotterydraw(Request $request): JsonResponse
    {
        $createdAt = $request->createdAt;
        $gameId = $request->gameId;
//        $gameName = Games::fetchSql()->find(7);
        if (!empty($createdAt) && !empty($gameId)) {
            $lottery_info = Lottery::where('game_id', '=', "{$gameId}")->where('is_open', '=', '1')->whereBetween('open_time', [[$createdAt . " 00:00:00", $createdAt . " 23:59:59"]])->orderBy('open_time', 'DESC')->get();
            return $this->success($lottery_info, '', '开奖结果查询成功');
        } else {
            return response()->json(['success' => false, 'message' => '彩种不可以为空！']);
        }
    }

    /**
     * @return JsonResponse
     * 获取所有彩种最近一期开奖结果
     */
    public function lotterydrawhis(): JsonResponse
    {
        $gameList = DB::table('lottery as t1')
            ->select('t1.*', 't2.game_name')
            ->leftJoin('games as t2', 't1.game_id', '=', 't2.id')
            ->where('t1.is_open', '=', 1)->orderBy('t1.open_time', 'DESC')->take(7)->get();
        if (!empty($gameList)) {
            return $this->success($gameList, '', '最后一期开奖结果查询成功');
        } else {
            return response()->json(['success' => false, 'message' => '查询失败！']);
        }
    }

    /**
     * @return JsonResponse
     * 彩种和值玩法配置
     */
    public function gamesetting(Request $request): JsonResponse
    {
        $gameId = $request->gameId;

        $nowTime = time();
        $gameList = DB::table('lottery')
            ->select('open_time', 'sort', 'issue', 'win_number')
            ->where('is_open', '=', 1)
            ->where('game_id', '=', $gameId)
            ->orderBy('open_time', 'DESC')
            ->take(1)
            ->get()->toArray();

        if (empty($gameList)) {
            return response()->json(['success' => false, 'message' => '历史开奖结果获取失败！']);
        }
        $time1 = strtotime($gameList[0]->open_time);
        //前端倒计时时间 $countdown
        $gameList[0]->countdown = 180 - ($nowTime - $time1);
        //判断当天最后一期第480
        if ($gameList[0]->sort == 480) {
            //加一 变成第二天第一期 $issueNow
            $gameList[0]->issueNow = intval(date('Ymd', strtotime(Carbon::tomorrow())) . '001');
        } else {
            $gameList[0]->issueNow = intval($gameList[0]->issue + 1);
        }

        $gameLists = DB::table('games_type as t1')
            ->select('t1.*', 't2.game_name', 't2.front_play_menu')
            ->leftJoin('games as t2', 't1.game_id', '=', 't2.id')
            ->where('t1.game_id', '=', $gameId)
            ->where('t1.is_open', '=', 1)
            ->get()
            ->toArray();
        if (empty($gameLists)) {
            return response()->json(['success' => false, 'message' => '彩种配置获取失败！']);
        }
        $data = array_merge($gameLists, $gameList);
        if (!empty($data)) {
            return $this->success($data, '', '彩种下注页面获取成功');
        } else {
            return response()->json(['success' => false, 'message' => '彩种下注页面获取失败！']);
        }
    }

    /**
     * 获取所有彩种信息
     * @return JsonResponse
     */
    public function lotterys(): JsonResponse
    {
        $games = Games::query();
        $res = $games->get()->toArray();
        if (!empty($res)) {
            return $this->success($res, '', '彩种信息获取成功');
        } else {
            return response()->json(['success' => false, 'message' => '彩种信息获取失败！']);
        }
    }

    /**
     * @return void
     * 个人盈亏 总下注金额
     */
    public function income()
    {
        $betOrders = BetOrders::query();
        $res = $betOrders->select('user_id', DB::raw('SUM(bet_money) as bet_sum_money'), DB::raw('SUM(win_money-bet_money) as sum_money'))
            ->groupBy('user_id')
            ->orderBy('sum_money', 'desc')
            ->get()->toArray();
        dd($res);
    }
}
