<?php

namespace App\Http\Controllers\Admin;

use App\Models\OperationLog;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Adminuser::select('name', 'id')->orderBy('id', 'desc')->get();
        $methods = OperationLog::$methods;
        return view('admin.operation.index', compact('users', 'methods'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = OperationLog::query();
        if (!empty($request->get('user_id'))) {
            $model = $model->where('user_id', $request->get('user_id'));
        }
        if (!empty($request->get('method'))) {
            $model = $model->where('method', $request->get('method'));
        }
//        if (!empty($request->get('path'))) {
//            $model = $model->where('path', 'like', $request->get('path') . '%');
//        }
        if (!empty($request->get('ip'))) {
            $model = $model->where('ip', 'like', $request->get('ip') . '%');
        }

        $res = $model->orderBy('id', 'desc')->with(['user'])->paginate($request->get('limit', 30))->toArray();
        $methodColors = OperationLog::$methodColors;
        foreach ($res['data'] as &$row) {
            $row['method_color'] = $methodColors[$row['method']] ?? 'red';
        }
        unset($row);
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        if ($id > 0) {
            $item = OperationLog::with(['user'])->findOrFail($id);

            if ($item['ip'] == '127.0.0.1') {
                $ipdata = [
                    'country' => '内网IP',
                    'region' => '',
                    'city' => ' ',
                    'county' => '',
                    'isp' => '内网IP',
                    'isp_id' => 'local',
                ];
            } else {
                $ipdata = $this->Get_Ip_To_City($item['ip']);
            }

            $arr = [];
            if (!empty($item['agent'])) {
                $agent = new Agent();
                $agent->setUserAgent($item['agent']);

                $arr = [
                    'device_name' => $agent->device(),
                    'system_name' => $agent->platform(),
                    'browser_name' => $agent->browser(),
                    'isRobot' => $agent->isRobot(),
                    'Robot_name' => $agent->robot(),
                    'languages' => implode('、', $agent->languages()),
                ];
                $arr['browser_version'] = $agent->version($arr['browser_name']);
                $arr['system_version'] = $agent->version($arr['system_name']);
            }

        }

        return view('admin.operation.show', compact('item', 'ipdata', 'arr'));
    }

    public function Get_Ip_To_City($ip = '')
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ip = json_decode(file_get_contents($url));
        if ((string)$ip->code == '1') {
            return false;
        }
        $data = (array)$ip->data;
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        if (OperationLog::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return response()->json(['code' => 1, 'msg' => '删除失败']);
    }

}
