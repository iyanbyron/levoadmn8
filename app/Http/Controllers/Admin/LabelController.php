<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LabelCreateRequest;
use App\Http\Requests\LabelUpdateRequest;
use App\Models\Label;
use App\Models\Videobigclass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LabelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.label.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = label::query();
        if (!empty($request->get('title'))) {
            //$model = $model->where('uid',$request->get('uid'));
            $model = $model->where('title', 'like', $request->get('title') . '%');
        }
        $res = $model->orderBy('id', 'desc')->paginate($request->get('limit', 15))->toArray();
        $res = label::leftjoin('video_bigclass', 'video_label.video_bigclass_id', '=', 'video_bigclass.id')
            ->select('video_label.id', 'video_label.label_name', 'video_label.created_at', 'video_label.updated_at', 'video_label.video_bigclass_id', 'video_bigclass.big_name')
            ->orderBy('video_label.id', 'desc')->paginate($request
                ->get('limit', 15))->toArray();

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
        $data = ['video_bigclass_id' => '0'];
        $data = (object)$data;
        $bigclass_list = Videobigclass::select('id', 'big_name')->get();
        return view('admin.label.create', compact('data', 'bigclass_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(LabelCreateRequest $request)
    {
        $data = $request->all();
        if (Label::create($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '添加标签成功'
                ]);
            } else {
                return redirect()->to(route('admin.news'))->with(['status' => '添加标签成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.label'))->withErrors('系统错误');
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
        $data = Label::findOrFail($id);
        $bigclass_list = Videobigclass::select('id', 'big_name')->get();
        return view('admin.label.edit', compact('data', 'bigclass_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(LabelUpdateRequest $request, $id)
    {
        $news = Label::findOrFail($id);
        $data = $request->except('字段');
        if ($news->update($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '更新信息成功'
                ]);
            } else {
                return redirect()->to(route('admin.news'))->with(['status' => '更新信息成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.news'))->withErrors('系统错误');
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
        if (Label::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return response()->json(['code' => 1, 'msg' => '删除失败']);
    }


}
