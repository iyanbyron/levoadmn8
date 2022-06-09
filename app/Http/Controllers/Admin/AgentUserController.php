<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\AdminUser;
use App\Models\AgentUserType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Support\Facades\Redis;

class AgentUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.agentuser.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = ['tid' => '0'];
        $user = (object)$user;
        $admin_type = AgentUserType::select('id', 'name', 'pid')->get();
        $admin_type_list = $this->admin_type_sort($admin_type);
        return view('admin.agentuser.create', compact('user', 'admin_type_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $data = $request->all();
        $data['uuid'] = \Faker\Provider\Uuid::uuid();
        $data['password'] = bcrypt($data['password']);
        $data['email'] = $data['email'] ?? '';
        $data['phone'] = $data['phone'] ?? '';
        $updata = Adminuser::create($data);
        Redis::hmset("agent_user:content:" . $updata['id'], $updata->toArray());
        if ($updata) {
            if (request()->ajax()) {
                //更新代理角色
                $user = AdminUser::findOrFail($updata['id']);
                $roles[2] = 4;//4:代理角色id
                $user->syncRoles($roles);
                return response()->json([
                    'status' => 'success',
                    'message' => '添加代理成功'
                ]);
            } else {
                return redirect()->to(route('admin.agentuser'))->with(['status' => '添加代理成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.agentuser'))->withErrors('系统错误');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Adminuser::findOrFail($id);
        $admin_type = AgentUserType::select('id', 'name', 'pid')->get();
        $admin_type_list = $this->admin_type_sort($admin_type);
        return view('admin.agentuser.edit', compact('user', 'admin_type_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $adminuser = AdminUser::findOrFail($id);
        $data = $request->except('password');
        if ($request->get('password')) {
            $data['password'] = bcrypt($request->get('password'));
        }
        if ($adminuser->update($data)) {
            Redis::hmset("agent_user:content:" . $adminuser['id'], $adminuser->toArray());
            if (request()->ajax()) {
                $roles[2] = 4;//4:代理角色id
                $adminuser->syncRoles($roles);
                return response()->json([
                    'status' => 'success',
                    'message' => '更新代理用户成功'
                ]);
            } else {
                return redirect()->to(route('admin.agentuser'))->with(['status' => '更新用户成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.agentuser'))->withErrors('系统错误');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return response()->json(['code' => 1, 'msg' => '请选择删除项']);
        }
        $list_data = [];
        $list_data = AdminUser::whereIn('id', $ids)->orderBy('id', 'desc')
            ->get()->toArray();
        foreach ($list_data as $key => $value) {
            Redis::del("agent_user:content:" . $value['id']);
        }
        if (Adminuser::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return response()->json(['code' => 1, 'msg' => '删除失败']);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = AdminUser::query();
        $model = $model->where('user_type', 1);
        $res = $model->orderBy('id', 'desc')->paginate($request->get('limit', 15))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }


    public function admin_type_sort($data, $pid = 0, $level = 0)
    {
        static $arr = array();
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['level'] = $level;
                if ($pid <> 0) {
                    $v['name'] = '|' . $this->topString($level) . $v['name'];
                } else {
                    $v['name'] = $this->topString($level) . $v['name'];
                }
                $arr[] = $v;
                $this->admin_type_sort($data, $v['id'], $level + 1);
            }
        }
        return $arr;
    }

    /**
     * 缩进
     */
    public function topString($level)
    {
        $str = '';
        for ($i = 0; $i < $level; $i++) {
            $str = $str . '-';
        }
        return $str;
    }
}
