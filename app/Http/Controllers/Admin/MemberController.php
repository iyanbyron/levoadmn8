<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberCreateRequest;
use App\Http\Requests\MemberUpdateRequest;
use App\Models\Bank;
use App\Models\Member;
use App\Models\UserBankcard;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use LogicException;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws \Throwable
     */
    public function index()
    {
        return view('admin.member.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $model = Member::query();
        if (!empty($request->get('username'))) {
            $model = $model->where('username', '=', $request->get('username'));
        }
        if (!empty($request->get('actual_name'))) {
            $model = $model->where('actual_name', '=', $request->get('actual_name'));
        }
        if (!empty($request->get('user_types'))) {
            if ($request->get('user_types') == 1) {
                $model = $model->where('user_type', 1);
            }
            if ($request->get('user_types') == 2) {
                $model = $model->where('user_type', 2);
            }
            if ($request->get('user_types') == 3) {
                $model = $model->where('user_type', 3);
            }
            if ($request->get('user_types') == 4) {
                $model = $model->where('user_type', 4);
            }
        }
        if (!empty($request->get('time_start'))) {
            $model = $model->where('created_at', '>=', $request->get('time_start'));
        }
        if ($request->get('time_end') !== null && !empty($request->get('time_end'))) {
            $model = $model->where('created_at', '<=', $request->get('time_end'));
        }
        $res = $model->orderBy('updated_at', 'desc')->paginate($request->get('limit', 15))->toArray();
        $data = [
            'code' => 0,
            'msg' => '???????????????...',
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
    public function store(MemberCreateRequest $request)
    {
        $data = $request->all();
        $user_data = Member::where('username', $data['username'])->first();
        if ($user_data) {
            return response()->json([
                'status' => 'fail',
                'message' => '????????????????????????????????????????????????'
            ]);
        }
        if ($data['user_type'] == '1' || $data['user_type'] == '3' || $data['user_type'] == '4') {
            if (trim($data['superior']) == "") {
                return response()->json([
                    'status' => 'fail',
                    'message' => '????????????????????????'
                ]);
            } else {
                $user_superior = Member::where('username', $data['superior'])->first();
                if (!$user_superior) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => '??????????????????????????????????????????????????????????????????'
                    ]);
                }
            }
        }
        $data['password'] = Hash::make($data['password']);
        if (empty($data['money_password'])) {
            $data['money_password'] = '';
        } else {
            $data['money_password'] = Hash::make($data['money_password']);
        }

        if ($data['user_type'] == 1) {
            $data['invitation_code'] = 0;
        } else {
            $code = DB::table('member')->max('invitation_code');
            $rand = rand(1, 9);
            $data['invitation_code'] = $code + $rand;
        }
        if (Member::create($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '??????????????????'
                ]);
            } else {
                return redirect()->to(route('admin.member'))->with(['status' => '??????????????????']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '????????????'
            ]);
        } else {
            return redirect()->to(route('admin.member'))->withErrors('????????????');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     * @throws \Throwable
     */
    public function create()
    {
        //$user = Adminuser::findOrFail($id);
        $user = ['user_type' => '1', 'status' => '1', 'id' => '0'];
        $data = [];
        $user = (object)$user;
        return view('admin.member.create', compact('user', 'data'));
    }

    /**
     * ???????????????????????????????????????.
     *
     * @param integer $min ?????????.
     * @param integer $max ?????????.
     * @param integer $len ??????????????????.
     *
     * @return array
     */
    public function uniqueRandom2(int $min, int $max, int $len): array
    {
        if ($min < 0 || $max < 0 || $len < 0) {
            throw new LogicException('???????????????');
        }
        if ($max <= $min) {
            throw new LogicException('??????????????????');
        }
        if (($max - $min + 2) < $len) {
            throw new LogicException("??????????????????????????????{$len}????????????????????????}");
        }
        $index = array();
        for ($i = $min; $i < $max + 1; $i++) {
            $index[$i] = $i;
        }
        $startOne = current($index);
        $endOne = end($index);
        for ($i = $startOne; $i < $endOne; $i++) {
            $one = mt_rand($i, $max);
            if ($index[$i] == $i) {
                $index[$i] = $index[$one];
                $index[$one] = $i;
            }
        }
        return array_slice($index, 0, $len);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = Member::findOrFail($id);
        return view('admin.member.edit', compact('user'));
    }

    public function bankcard($id, $username)
    {
        //echo  $username;
        $data = [];
        $data = (object)$data;
        $data->user_id = $id;
        $data->username = $username;
        return view('admin.member.bankcard', compact('data'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function bankcard_data(Request $request)
    {
        //$all = $request->all();
        $model = UserBankcard::query();
        if (!empty($request->get('user_id'))) {
            $model = $model->where('user_id', $request->get('user_id'));
        }
        $res = $model->orderBy('id', 'desc')->paginate($request->get('limit', 50))->toArray();
        $data = [
            'code' => 0,
            'msg' => '???????????????...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function bankcardDestroy(Request $request)
    {
        $ids = $request->get('id');
        if (empty($ids)) {
            return response()->json(['code' => 1, 'msg' => '??????????????????']);
        }
        if (UserBankcard::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '????????????']);
        }
        return response()->json(['code' => 1, 'msg' => '????????????']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return response()->json(['code' => 1, 'msg' => '??????????????????']);
        }
        if (UserBankcard::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '????????????']);
        }
        return response()->json(['code' => 1, 'msg' => '????????????']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function bankcardCreate(Request $request)
    {
        $user_id = $request->user_id;
        $users = Member::where('id', $user_id)->first();
        $data = ['user_id' => $user_id, 'username' => $users->username, 'bank_name' => ''];
        $data = (object)$data;
        $bank = Bank::get();
        return view('admin.member.bankcardcreate', compact('data', 'bank'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function bankcardStore(Request $request)
    {
        $data = $request->all();
        if (UserBankcard::create($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '?????????????????????'
                ]);
            } else {
                return redirect()->to(route('admin.member.bankcard'))->with(['status' => '?????????????????????']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '????????????'
            ]);
        } else {
            return redirect()->to(route('admin.game'))->withErrors('????????????');
        }

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function bankcardEdit($id)
    {
        $data = UserBankcard::findOrFail($id);
        $bank = Bank::get();
        return view('admin.member.bankcardedit', compact('data', 'bank'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function bankcardUpdate(Request $request, $id)
    {
        $news = UserBankcard::findOrFail($id);
        $data = $request->except('??????');
        if ($news->update($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '??????????????????'
                ]);
            } else {
                return redirect()->to(route('admin.member.bankcard'))->with(['status' => '??????????????????']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '????????????'
            ]);
        } else {
            return redirect()->to(route('admin.member.bankcard'))->withErrors('????????????');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function update(MemberUpdateRequest $request, $id)
    {
        $user = Member::findOrFail($id);
        //return response()->json($user);
        $data = $request->except(['username']);
        //print_r($data);exit;
        $money = $request->input('money');
        if ($money != "") {
            //$data['money'] = $user['money'] + $money;
        }

        if (trim($data['superior']) == "") {
            unset($data['superior']);
        }

        if (trim($data['money_password']) == "") {
            unset($data['money_password']);
        } else {
            $data['money_password'] = Hash::make($data['money_password']);;
        }

        if (trim($data['password']) == "") {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);;
        }
        if ($user->update($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '??????????????????'
                ]);
            } else {
                return redirect()->to(route('admin.member'))->with(['status' => '??????????????????']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '????????????'
            ]);
        } else {
            return redirect()->to(route('admin.member'))->withErrors('????????????');
        }
    }

    /**
     * ??????????????????
     * @param Request $request
     * @return JsonResponse
     */
    public function isuse(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return response()->json(['code' => 1, 'msg' => '??????????????????']);
        }
        $user = Member::where('id', $ids)->first();
        $data['status'] = ($user['status'] == 1) ? 0 : 1;
        if ($user->whereIn('id', $ids)->update($data)) {
            return response()->json(['code' => 0, 'msg' => '????????????']);
        }
        return response()->json(['code' => 1, 'msg' => '????????????']);
    }
}
