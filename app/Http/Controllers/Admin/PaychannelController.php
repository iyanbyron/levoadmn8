<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaychannelCreateRequest;
use App\Http\Requests\PaychannelUpdateRequest;
use App\Models\Paychannel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class PaychannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.paychannel.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = Paychannel::query();
        if (!empty($request->get('title'))) {
            //$model = $model->where('uid',$request->get('uid'));
            $model = $model->where('pr_title', 'like', $request->get('title') . '%');
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = ['id' => '0', 'is_open' => 0, 'pay_is_rend' => 0];
        $data = (object)$data;
        return view('admin.paychannel.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaychannelCreateRequest $request)
    {
        $data = $request->all();
        if ($paychannel = Paychannel::create($data)) {
            if (request()->ajax()) {
                Redis::hmset("paychannel:content:" . $paychannel['pay_type'], $paychannel->toArray());
                return response()->json([
                    'status' => 'success',
                    'message' => '添加渠道成功'
                ]);
            } else {
                return redirect()->to(route('admin.paychannel'))->with(['status' => '添加渠道成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.paychannel'))->withErrors('系统错误');
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
        $data = Paychannel::findOrFail($id);
        return view('admin.paychannel.edit', compact('data'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PaychannelUpdateRequest $request, $id)
    {
        $paychannel = Paychannel::findOrFail($id);
        $data = $request->except('字段');
        if ($paychannel->update($data)) {
            if (request()->ajax()) {
                //Redis::del("paychannel:content:" .$paychannel['pay_type']);
                Redis::hmset("paychannel:content:" . $paychannel['pay_type'], $paychannel->toArray());
                return response()->json([
                    'status' => 'success',
                    'message' => '更新信息成功'
                ]);
            } else {
                return redirect()->to(route('admin.paychannel'))->with(['status' => '更新信息成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.paychannel'))->withErrors('系统错误');
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
        $list_data = Paychannel::whereIn('id', $ids)->orderBy('id', 'desc')
            ->get()->toArray();
        foreach ($list_data as $key => $value) {
            Redis::del("paychannel:content:" . $value['pay_type']);
        }
        if (Paychannel::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return response()->json(['code' => 1, 'msg' => '删除失败']);
    }


}
