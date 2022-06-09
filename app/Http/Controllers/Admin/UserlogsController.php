<?php

namespace App\Http\Controllers\Admin;

use App\Models\Userlogs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserlogsController extends Controller
{
    /**
     * 用户登陆列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.userlogs.index');
    }

    /**
     * 用户登陆数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $all = $request->all();
        $model = Userlogs::query();
        if (!empty($all['username'])) {
            $model = $model->where('username', 'like', '%' . $request->get('username') . '%');
        }
        if (!empty($all['login_ip'])) {
            $model = $model->where('login_ip', 'like', '%' . $request->get('login_ip') . '%');
        }

        if (!empty($all['time_start'])) {
            $model = $model->where('created_at', '>=', $all['time_start']);
        }
        if (isset($all['time_end']) && !empty($all['time_end'])) {
            $model = $model->where('created_at', '<=', $all['time_end']);
        }

        if (isset($all['type']) && !empty($all['type'])) {
            $model = $model->where('type', $all['type']);
        }

        $res = $model->orderBy('id', 'desc')->paginate($request->get('limit', 15))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

}
