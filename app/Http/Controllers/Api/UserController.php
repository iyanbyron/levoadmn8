<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Models\Bank;
use App\Models\BetOrders;
use App\Models\Lottery;
use App\Models\Member;
use App\Models\Orders;
use App\Models\UserAccountChange;
use App\Models\UserBankcard;
use App\Models\Userlogs;
use App\Models\Withdrawal;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * 定义jwt验证$guard参数
     * @var string
     */
    protected $guard = 'api';

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login', 'vcode', 'random', 'link']]);
    }

    /**
     * @param Request $request
     * 删除银行卡
     * @return JsonResponse
     */
    public function bankdelete(Request $request): JsonResponse
    {
        $input = $request->all();
        $id = $input['bankCardId'];
        $bank = UserBankcard::where('id', $id)->first();
        if (empty($bank)) {
            return response()->json(['success' => false, 'message' => '银行卡信息不存在！']);
        }
        $user_id_count = UserBankcard::where('user_id', $bank->user_id)->count('user_id');
        if ($user_id_count < 2) {
            return response()->json(['success' => false, 'message' => '用户需保留一张银行卡！']);
        }
        $update = DB::table('user_bank_card')
            ->where('id', $id)
            ->delete();
        if (!empty($update)) {
            return $this->success('', '', '银行卡信息删除成功');
        } else {
            return response()->json(['success' => false, 'message' => '银行卡信息删除失败！']);
        }
    }

    /**
     * @param Request $request
     * 用户读取站内信
     * @return JsonResponse
     */
    public function usergbookid(Request $request): JsonResponse
    {
        $userid = $request->userid;
        $bookid = $request->bookid;
        $update = DB::table('user_gbook')
            ->where('user_id', $userid)
            ->where('id', $bookid)
            ->update(['is_reply' => 1,
                'updated_at' => date('Y-m-d H:i:s', time()),
            ]);
        if (!empty($update)) {
            return $this->success('', '', '查看站内信成功!');
        } else {
            return response()->json(['success' => false, 'message' => '查看站内信失败！']);
        }
    }

    /**
     * @return JsonResponse
     * 获取链接
     */
    public function link(): JsonResponse
    {
        $link = DB::table('sites')->where('id', 1)->pluck('value');
        //dd($link[0]);
        $linkArray = json_decode($link[0], true);
        return $this->success($linkArray, '', '查询成功');
    }

    /**
     * @param Request $request
     * 用户获取站内信
     * @return JsonResponse
     */
    public function usergbook(Request $request): JsonResponse
    {
        $id = $request->userid;
        $usergbook = DB::table('user_gbook')->where('user_id', $id)->get();
        if (!empty($usergbook)) {
            return $this->success($usergbook, '', '获取站内信成功!');
        } else {
            return response()->json(['success' => false, 'message' => '获取站内信失败！']);
        }
    }

    /**
     * @return JsonResponse
     * 实时播报随机 30条
     */
    public function random(): JsonResponse
    {
        $data = [];
        $titles = DB::table('games')->pluck('game_name')->toArray();
        for ($i = 1; $i <= 30; $i++) {
            //随机彩种
            shuffle($titles);
            $data[$i]['lotteryName'] = $titles[0];
            //随机名字
            $str = 'abcdefghijklmnopqrstuvwxyz123456789';
            $randStr = str_shuffle($str);//打乱字符串
            $data[$i]['username'] = substr($randStr, 0, 4) . '***';//substr(string,start,length);返回字符串的一部分
            //随机金额
            $data[$i]['win_coin'] = rand(101, 100000);
        }
        return $this->success($data, '', '查询成功');

    }

    /**
     * @param Request $request
     * 个人订单查询
     * @return JsonResponse
     */
    public function ordersfind(Request $request): JsonResponse
    {
        $username = $request->username;
        $startTime = $request->startTime;
        $isOpen = $request->isOpen;
        $gameId = $request->gameId;
        $wheres = [];
        if (!empty($username)) {
            $wheres[] = ['username', '=', $username];
        }
        if (!empty($type)) {
            $wheres[] = ['is_open', '=', $isOpen];
        }
        if (!empty($gameId)) {
            $wheres[] = ['game_id', '=', $gameId];
        }

        if (!empty($startTime)) {
            $list = BetOrders::where($wheres)->whereBetween('updated_at', [[$startTime . " 00:00:00", $startTime . " 23:59:59"]])->orderBy('updated_at', 'DESC')->get()->toArray();
        } else {
            $list = BetOrders::where($wheres)->orderBy('updated_at', 'DESC')->get()->toArray();
        }
        return $this->success($list, '', '个人订单查询成功');
    }

    /**
     * @param Request $request
     * 兑换列表
     * @return JsonResponse
     */
    public function withdrawlist(Request $request): JsonResponse
    {
        $user_id = $request->userId;
        $startTime = $request->startTime;
        $endTime = $request->endTime;

        $withdrawal = new Withdrawal();
        if (!empty($endTime)) {
            $list = $withdrawal->where('user_id', $user_id)->whereBetween('created_at', [[$startTime . " 00:00:00", $endTime . " 23:59:59"]])->orderBy('updated_at', 'DESC')->get()->toArray();
        } else {
            $list = $withdrawal->where('user_id', $user_id)->orderBy('updated_at', 'DESC')->get()->toArray();
        }
        return $this->success($list, '', '查询成功');
    }

    /**
     * @param Request $request
     * 兑换 -> 提现
     * @return JsonResponse
     */
    public function withdraw(Request $request): JsonResponse
    {
        $user_id = $request->userId;
        $bank_id = $request->bankCode;
        $withdrawMoney = $request->withdrawMoney;
        $money_password = $request->moneyPassword;
        if(!is_int($withdrawMoney)){
            return response()->json(['success' => false, 'message' => '兑换金额必须是整数！']);
        }
        if ($withdrawMoney < 100) {
            return response()->json(['success' => false, 'message' => '最少兑换100！']);
        }
        $userBankCard = new UserBankcard();
        $userBankCardList = $userBankCard->where(['user_id' => $user_id, 'id' => $bank_id])->first();
        if (empty($userBankCardList)) {
            return response()->json(['success' => false, 'message' => '暂无绑定银行卡！']);
        }
        $member = new Member();
        $memberList = $member->where('id', $user_id)->first();
        if ($memberList->money - $withdrawMoney < 0) {
            return response()->json(['success' => false, 'message' => '提现金额不足！']);
        }
        if ($memberList->code_amount != 0) {
            return response()->json(['success' => false, 'message' => '抱歉，您的打码额不够，请咨询客服!']);
        }
        if (empty($memberList)) {
            return response()->json(['success' => false, 'message' => '暂无用户信息！']);
        }
        if (!Hash::check($money_password, $memberList->money_password)) {
            return response()->json(['success' => false, 'message' => '资金密码填写错误！']);
        }
        $order = date('YmdHis' . mt_rand(100000, 999999));

        $data = [
            'status' => 0,
            'withdraw_order' => $order,
            'amount' => $withdrawMoney,
            'type' => 1,
            'bank_name' => $userBankCardList->bank_name,
            'subbranch_name' => $userBankCardList->subbranch_name,
            'bank_card_number' => $userBankCardList->bank_card_number,
            'account_name' => $userBankCardList->account_name,
            'user_id' => $memberList->id,
            'username' => $memberList->username,
            'finish_time' => date('Y-m-d H:i:s', time()),
        ];
        $withdraw = new Withdrawal();
        $add = $withdraw->create($data);
        if (empty($add)) {
            return response()->json(['success' => false, 'message' => '兑换失败！']);
        } else {
            $update = DB::table('member')
                ->where('id', $user_id)
                ->update(
                    ['money' => $memberList->money - $withdrawMoney,
                        'withdraw_today' => $memberList->withdraw_today + $withdrawMoney,
                        'histor_withdraw' => $memberList->histor_withdraw + $withdrawMoney,
                        'updated_at' => date('Y-m-d H:i:s', time()),
                        'frozen_money' => $withdrawMoney
                    ]);

            $add = [
                'username' => $memberList->username,
                'actual_name' => $memberList->actual_name,
                'type' => 6,
                'play' => '兑换',
                'issue' => '0',
                'order_num' => $order,
                'game_name' => '兑换',
                'bet' => '',
                'bet_money' => $withdrawMoney,
                'money' => $memberList->money - $withdrawMoney,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            $userBankcard = new UserAccountChange();
            //往数据库批量插入数据
            $userBankcard::create($add);
            if (!empty($update)) {
                return $this->success('', '', '兑换申请中');
            } else {
                return response()->json(['success' => false, 'message' => '用户兑换金额失败！']);
            }
        }
    }

    /**
     * @param Request $request
     * 帐变
     * @return JsonResponse
     */
    public function useraccountchange(Request $request): JsonResponse
    {
        $username = $request->username;
        $startTime = $request->startTime;
        $endTime = $request->endTime;
        $type = $request->type;
        $wheres = [];
        if (!empty($username)) {
            $wheres[] = ['username', '=', $username];
        }
        if (!empty($type)) {
            $wheres[] = ['type', '=', $type];
        }
        $userAccountChange = new UserAccountChange();
        if (!empty($endTime)) {
            $list = $userAccountChange->where($wheres)->whereBetween('created_at', [[$startTime . " 00:00:00", $endTime . " 23:59:59"]])->orderBy('id', 'DESC')->get()->toArray();
        } else {
            $list = $userAccountChange->where($wheres)->orderBy('id', 'DESC')->get()->toArray();
        }
        return $this->success($list, '', '个人帐变查询成功');
    }

    /**
     * @param Request $request
     * 充值
     * @return JsonResponse
     */
    public function orderchange(Request $request): JsonResponse
    {
        $user_id = $request->userId;
        $startTime = $request->startTime;
        $endTime = $request->endTime;
        $order_num = $request->orderNum;
        $wheres = [];

        if (!empty($user_id)) {
            $wheres[] = ['user_id', '=', $user_id];
        }
        if (!empty($order_num)) {
            $wheres[] = ['order_num', '=', $order_num];
        }
        $orders = new Orders();
        if (!empty($endTime)) {
            $list = $orders->where($wheres)->whereBetween('created_at', [[$startTime . " 00:00:00", $endTime . " 23:59:59"]])->get();
        } else {
            $list = $orders->where($wheres)->get();
        }
        return $this->success($list, '', '充值记录查询成功');
    }

    /**
     * @param Request $request
     * 修改银行卡信息
     * @return JsonResponse
     */
    public function bankupdate(Request $request): JsonResponse
    {
        $input = $request->all();

        $id = $input['bankCardId'];
        $bank = UserBankcard::where('id', $id)->first();
        if (empty($bank)) {
            return response()->json(['success' => false, 'message' => '银行卡信息不存在！']);
        }
        $update = DB::table('user_bank_card')
            ->where('id', $id)
            ->update(['subbranch_name' => $input['subbranchName'],
                'bank_card_number' => $input['bankCardNumber'],
                'bank_code' => $input['bankCode'],
                'username' => $input['username'],
                'bank_name' => $input['bankName'],
                'account_name' => $input['accountName'],
                'user_id' => $input['userId'],
                'updated_at' => date('Y-m-d H:i:s', time()),
            ]);
        if (!empty($update)) {
            return $this->success('', '', '银行卡信息修改成功');
        } else {
            return response()->json(['success' => false, 'message' => '银行卡信息修改失败！']);
        }
    }

    /***
     * @param Request $request
     * 获取用户银行卡
     * @return JsonResponse
     */
    public function bankcardlist(Request $request): JsonResponse
    {
        $user_id = $request->userId;
        $list = UserBankcard::where('user_id', $user_id)->orderBy('updated_at', 'DESC')->get()->toArray();
        return $this->success($list, '', '成功获取用户银行卡列表');
    }

    /**
     * @param Request $request
     * 添加银行卡
     * @return JsonResponse
     */
    public function bankcardadd(Request $request): JsonResponse
    {
        $user_id = $request->userId;
        $bank_code = $request->bankId;
        $bank_name = $request->bankName;
        $subbranch_name = $request->subbranchName;
        $account_name = $request->accountName;
        $bank_card_number = $request->bankCardNumber;
        $check_bank_card_number = $request->checkBankCardNumber;
        $money_password = $request->moneyPassword;
//        if (empty($subbranch_name)) {
//            return response()->json(['success' => false, 'message' => '开户支行不能为空！']);
//        }
        if (empty($account_name)) {
            return response()->json(['success' => false, 'message' => '开户人不能为空！']);
        }
        if (empty($bank_card_number)) {
            return response()->json(['success' => false, 'message' => '银行卡号不能为空！']);
        }
        if (empty($check_bank_card_number)) {
            return response()->json(['success' => false, 'message' => '请再次输入银行卡号！']);
        }
        if (empty($money_password)) {
            return response()->json(['success' => false, 'message' => '请输入资金密码！']);
        }
        if ($bank_card_number !== $check_bank_card_number) {
            return response()->json(['success' => false, 'message' => '前后卡号不一样！']);
        }
        $member = Member::where('id', $user_id)->first();
        if ($member->actual_name !== $account_name) {
            return response()->json(['success' => false, 'message' => '开户人必须是本人！']);
        }
        if (!Hash::check($money_password, $member->money_password)) {
            return response()->json(['success' => false, 'message' => '资金密码填写错误！']);
        }
        $res = UserBankcard::where('bank_card_number', $bank_card_number)->first();
        if (!empty($res)) {
            return response()->json(['success' => false, 'message' => '银行卡号已存在！']);
        }

        $data = [
            'user_id' => $user_id,
            'username' => $account_name,
            'bank_code' => $bank_code,
            'bank_name' => $bank_name,
            'subbranch_name' => $subbranch_name,
            'bank_card_number' => $bank_card_number,
            'account_name' => $account_name,
        ];
        $userBankcard = new UserBankcard();
        //往数据库批量插入数据
        $result = $userBankcard::create($data);
        if (empty($result)) {
            DB::rollBack();
        }
        return $this->success($result, '', '银行卡绑定成功');
    }

    /**
     * @return JsonResponse
     * 获取银行列表
     */
    public function banklist(): JsonResponse
    {
        $games = Bank::query();
        $res = $games->where('status', '=', 1)->get()->toArray();
        if (!empty($res)) {
            return $this->success($res, '', '银行列表获取成功');
        } else {
            return response()->json(['success' => false, 'message' => '银行列表获取失败！']);
        }
    }

    /**
     * @param Request $request
     * 修改资金密码
     * @return JsonResponse
     */
    public function updatemoneypassword(Request $request): JsonResponse
    {
        $id = $request->id;
        $moneyPassword = $request->moneyPassword;
        $newMoneyPassword = $request->newMoneyPassword;
        $checkMoneyPassword = $request->checkMoneyPassword;
        $member = Member::where('id', $id)->first();
        if (empty($member)) {
            return response()->json(['success' => false, 'message' => '用户不存在！']);
        }
        if (empty($newMoneyPassword)) {
            return response()->json(['success' => false, 'message' => '新密码不能为空！']);
        }
        if (empty($checkMoneyPassword)) {
            return response()->json(['success' => false, 'message' => '请确认资金密码！']);
        }
        if ($newMoneyPassword !== $checkMoneyPassword) {
            return response()->json(['success' => false, 'message' => '前后密码不一样！']);
        }
        if ($member->is_password != 0) {
            if (!Hash::check($moneyPassword, $member->money_password)) {
                return response()->json(['success' => false, 'message' => '资金密码填写错误！']);
            }
        }
        $newMoneyPasswords = Hash::make($newMoneyPassword);
        $update = DB::table('member')
            ->where('id', $id)
            ->update(['money_password' => $newMoneyPasswords, 'is_password' => 1]);
        if (empty($update)) {
            return response()->json(['success' => false, 'message' => '资金密码更新失败！']);
        } else {
            return $this->success('', '', '资金密码更新成功');
        }
    }

    /**
     * @param Request $request
     * 修改密码
     * @return JsonResponse
     */
    public function updatepassword(Request $request)
    {
        $input = $request->all();  #获取所有参数
        $member = Member::where('id', $input['id'])->first();
        if (empty($member)) {
            return response()->json(['success' => false, 'message' => '用户不存在！']);
        }
        if (!Hash::check($input['password'], $member->password)) {
            return response()->json(['success' => false, 'message' => '密码填写错误！']);
        }
        if ($input['newPassword'] !== $input['checkPassword']) {
            return response()->json(['success' => false, 'message' => '前后密码不一样！']);
        }
        $newPassword = Hash::make($input['newPassword']);

        $update = DB::table('member')
            ->where('id', $input['id'])
            ->update(['password' => $newPassword, 'updated_at' => date('Y-m-d H:i:s', time())]);
        if (empty($update)) {
            return response()->json(['success' => false, 'message' => '密码更新失败！']);
        } else {
            return $this->success('', '', '密码更新成功');
        }
    }

    /***
     * @param Request $request
     * 更新会员信息
     * @return JsonResponse
     */
    public function updateuserinfo(Request $request): JsonResponse
    {
        $data['username'] = $request->username;
        $data['actual_name'] = $request->actual_name;
        $data['qq'] = $request->qq;
        $data['gender'] = $request->gender;
        $data['mobile'] = $request->mobile;
        $data['updated_at'] = date('Y-m-d H:i:s', time());

        $res = DB::table('member')->where('id', $request->id)->update($data);
        if (empty($res)) {
            return response()->json(['success' => false, 'message' => '个人信息更新失败！']);
        } else {
            return $this->success($res, '', '个人信息更新成功');
        }
    }

    /***
     * @param Request $request
     * 获取会员个人信息
     * @return JsonResponse
     */
    public function getuser(Request $request): JsonResponse
    {
        $user_id = $request->id ?? '';
        if (!empty($user_id)) {
            $user_info = Member::where('id', '=', "{$user_id}")->first();
            if (empty($user_info)) {
                return $this->fail('获取用户信息异常！', "400");
            }
            $token = JWTAuth::fromUser($user_info);
            return $this->success($user_info, $token, '获取个人信息成功');
        } else {
            return $this->fail('用户id不能为空！', "400");
        }
    }

    /***
     * 前端登陆注册获取验证码
     * @return JsonResponse
     */
    public function vcode(): JsonResponse
    {
        session_start();

        $_SESSION['authcode'] = rand(1000, 9999);
        return $this->success($_SESSION['authcode'], '', '获取验证码成功');
    }

    /***
     * 前台会员注册
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $invitation_code = $request->invitation_code;
        $username = $request->username;
        $password = $request->password;
        $check_password = $request->check_password;
        $actual_name = $request->actual_name;
        $code = $request->code;

        if (!empty($code)) {
            if (isset($request->code)) {
                session_start();
                if (($code != $_SESSION['authcode'])) {
                    return response()->json(['success' => false, 'message' => '验证码错误！']);
                }
            }
        } else {
            return response()->json(['success' => false, 'message' => '验证码必填！']);
        }

        if (empty($username)) {
            return response()->json(['success' => false, 'message' => '用户名必填！']);
        }

        if (empty($password)) {
            return response()->json(['success' => false, 'message' => '密码必填！']);
        }

        if (empty($check_password)) {
            return response()->json(['success' => false, 'message' => '第二次密码必填！']);
        }

        if (empty($actual_name)) {
            return response()->json(['success' => false, 'message' => '真实姓名必填！']);
        }

        if (empty($invitation_code)) {
            return response()->json(['success' => false, 'message' => '邀请码必填！']);
        }

        if ($check_password != $password) {
            return response()->json(['success' => false, 'message' => '两次密码输入不一致！']);
        }

        $data = Member::where('invitation_code', $invitation_code)->first();
        if (empty($data)) {
            return response()->json(['success' => false, 'message' => '邀请码错误！']);
        }

        $member = Member::where('username', $username)->first();
        if (!empty($member)) {
            return response()->json(['success' => false, 'message' => '用户名已被注册！']);
        }

        $password = Hash::make($request->password);
        $user = Member::create([
            'username' => $username,
            'password' => $password,
            'actual_name' => $actual_name,
            'birthday' => date('Y-m-d H:i:s'),
//            'invitation_code' => rand(10000,99999),
            'superior' => $data->username,
        ]);

        JWTAuth::fromUser($user);
        return $this->success($user, '', '注册成功');
    }


    /***
     * 前台会员登录
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $username = $request->username;
        $password = $request->password;

        if (empty($username)) {
            return response()->json(['success' => false, 'message' => '账号不可以为空！']);
        }

        if (empty($password)) {
            return response()->json(['success' => false, 'message' => '密码不可以为空！']);
        }

        $member = Member::where('username', $username)->first();
        if (!$member) {
            return response()->json(['success' => false, 'message' => '此账号不存在！']);
        }
        if ($member->status == 0) {
            return response()->json(['success' => false, 'message' => '此账号已禁用！']);
        }
        if (!Hash::check($password, $member->password)) {
            return response()->json(['success' => false, 'message' => '密码填写错误！']);
        }

        $request_data = ['username' => $username, 'password' => $request->password];
        $credentials = $request_data;
        $data_logs['username'] = $username;
        $data_logs['login_ip'] = getClientIp();
        $data_logs['browser'] = get_broswer();
        $data_logs['login_domain'] = $_SERVER['SERVER_NAME'];
        Userlogs::create($data_logs);
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        DB::table('member')
            ->where('username', $username)
            ->update(['logintime' => time(), 'loginip' => getClientIp()]);
        $members = Member::where('username', $username)->first();
        $this->respondWithToken($token);
        return $this->success($members, $token, '登陆成功');
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Log the api out (Invalidate the token).
     * 会员退出
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->success('', '', 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $token = Auth::guard($this->guard)->getToken()->get();
        return response()->json($user);
        // return response()->json(auth('api')->user());
    }

    /**
     * @param Request $request
     * 会员下注
     * @return JsonResponse
     */
    public function betOrders(Request $request)
    {
        $input = $request->all();  #获取所有参数
        $id = $input['userId'];
        $origin = $input['origin'];
        $game_id = $input['game_id'];
        $issue = $input['issue'];
        $created_at = date('Y-m-d H:i:s',time());
        $lottery = Lottery::where(['game_id' => $game_id, 'issue' => $issue])->first();
        if ($created_at >= $lottery->open_time) {
            return response()->json(['success' => false, 'message' => '当前期数已结束,请刷新页面获取最新数据']);
        }
        if (!empty($id)) {
            $member = Member::where('id', '=', $id)->first();
            if ($member->money >= $input['totalMoney']) {
                foreach ($input['betNumber'] as $k => $v) {
                    $member = Member::where('id', '=', $id)->first();
                    //订单号  时间 +  随机数
                    $order = date('YmdHis' . mt_rand(100000, 999999));
                    if ($member->code_amount - $v['price'] < 0) {
                        $code_amount = 0;
                    } else {
                        $code_amount = $member->code_amount - $v['price'];
                    }
                    $add = [
                        'username' => $member->username,
                        'actual_name' => $member->actual_name,
                        'type' => 3,
                        'play' => $v['front_play_menu'] . '-' . $v['game_name'],
                        'issue' => $input['issue'],
                        'order_num' => $order,
                        'game_name' => $input['game_name'],
                        'bet' => $v['game_type'],
                        'bet_money' => $v['price'],
                        'money' => $member->money - $v['price'],
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                    ];
                    //往数据库批量插入数据
                    DB::table('user_account_change')->insert($add);

                    DB::table('member')
                        ->where('id', $id)
                        ->update(['money' => $add['money'], 'code_amount' => $code_amount, 'updated_at' => date('Y-m-d H:i:s', time())]);

                    $create = [
                        'user_id' => $id,
                        'order' => $order,
                        'username' => $member->username,
                        'bet' => $v['game_type'],
                        'single_money' => $v['price'],
                        'game_name' => $input['game_name'],
                        'odds' => $v['odds'],
                        'origin' => $origin,
                        'is_open' => 0,
                        'bet_money' => $v['price'],
                        'game_id' => $v['game_id'],
                        'issue' => $input['issue'],
                        'play' => $v['front_play_menu'] . '-' . $v['game_name'],
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                    ];
                    $res = DB::table('bet_orders')->insert($create);
                    if (empty($res)) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => '下单失败,请刷新获取最新数据！']);
                    }
                }
            } else {
                return response()->json(['success' => false, 'message' => '金额不足，请充值！']);
            }
            return $this->success('', '', '下单成功');
        } else {
            return response()->json(['success' => false, 'message' => '用户不存在！']);
        }
    }
}




