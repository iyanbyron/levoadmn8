<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NewsCreateRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Models\Member;
class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.news.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = News::query();
        if (!empty($request->get('title'))) {
            //$model = $model->where('uid',$request->get('uid'));
            $model = $model->where('title', 'like', $request->get('title') . '%');
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
        //$user = Adminuser::findOrFail($id);
        return view('admin.news.create', ['name' => 'Victoria']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsCreateRequest $request)
    {
        $data = $request->all();
        $admin_name = auth()->user()->username;
            if (request()->ajax()) {
                if($data['news_type']=='0')
                {
                    $user =Member::select('username','id')->get()->toArray();
                    foreach ($user  as $key=>$values)
                    {
                        $datas=[
                            'title'=>$data['title'],
                            'content'=>$data['content'],
                            'user_id'=>$values['id'],
                            'username'=>$values['username'],
                            'admin_name'=>$admin_name,
                        ];
                        News::create($datas);
                    }
                }else
                {
                    $user_data = Member::where('username', '=', $data['username'])->first();
                    if($user_data)
                    {
                        $datas=[
                            'title'=>$data['title'],
                            'content'=>$data['content'],
                            'user_id'=>$user_data->id,
                            'username'=>$data['username'],
                            'admin_name'=>$admin_name,
                        ];
                        News::create($datas);
                    }else
                    {
                        return response()->json([
                            'status' => 'fail',
                            'message' => '发送信息失败,用户名不存在'
                        ]);
                    }

                }
                return response()->json([
                    'status' => 'success',
                    'message' => '添加公告成功'
                ]);
            } else {
                return redirect()->to(route('admin.news'))->with(['status' => '添加公告成功']);
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
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewsUpdateRequest $request, $id)
    {
        $news = News::findOrFail($id);
        $data = $request->except('字段');
        $data['is_reply']=1;
        $admin_name = auth()->user()->username;
        $data['admin_name']=$admin_name;
        if ($news->update($data)) {
            if (request()->ajax()) {
                $notice_data = News::orderBy('updated_at', 'desc')
                    ->first();
                return response()->json([
                    'status' => 'success',
                    'message' => '回复成功'
                ]);
            } else {
                return redirect()->to(route('admin.news'))->with(['status' => '回复成功']);
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
        if (News::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return response()->json(['code' => 1, 'msg' => '删除失败']);
    }


}
