<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GamesCreateRequest;
use App\Http\Requests\GamesUpdateRequest;
use App\Models\Games;
use App\Models\GamesType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use phpDocumentor\Reflection\Types\Object_;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.game.index');
    }

    /**
     * Display a listing of the resource.
     *游戏玩法
     * @return \Illuminate\Http\Response
     */
    public function gameType($id)
    {
        $data = Games::findOrFail($id);
        return view('admin.game.type', compact('data'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function gameTypeData(Request $request)
    {
        $all = $request->all();
        $model = GamesType::query();
        $model = $model->where('game_id', $all['id']);
        $res = $model->orderBy('id', 'desc')->paginate($request->get('limit', 200))->toArray();
        foreach ($res['data'] as $key => $val) {
            $res['data'][$key]['game_name'] =$all['game_name'];
        }

        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $all = $request->all();
        $model = Games::query();
        if (isset($all['is_open']) ) {
            if ($all['is_open']<>"" &&  $all['is_open']<>3) {
                $model = $model->where('game_status', $all['is_open']);
            }
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
        $data = ['game_status' => '1'];
        $data = (object)$data;
        return view('admin.game.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(GamesCreateRequest $request)
    {
        $data = $request->all();
        if (Games::create($data)) {
            if (request()->ajax()) {
                $notice_key_redis = "games:content:game";
                $notice_data = Games::orderBy('updated_at', 'desc')
                    ->first();
                Redis::hmset($notice_key_redis, $notice_data->toArray());
                return response()->json([
                    'status' => 'success',
                    'message' => '添加游戏成功'
                ]);
            } else {
                return redirect()->to(route('admin.game'))->with(['status' => '添加游戏成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.game'))->withErrors('系统错误');
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
        $data = Games::findOrFail($id);
        return view('admin.game.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(GamesUpdateRequest $request, $id)
    {
        $news = Games::findOrFail($id);
        $data = $request->except('字段');
        if ($news->update($data)) {
            if (request()->ajax()) {
                $notice_key_redis = "news:content:notice";
                $notice_data = Games::orderBy('updated_at', 'desc')
                    ->first();
                //Redis::hmset($notice_key_redis, $notice_data->toArray());
                return response()->json([
                    'status' => 'success',
                    'message' => '更新信息成功'
                ]);
            } else {
                return redirect()->to(route('admin.game'))->with(['status' => '更新信息成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.game'))->withErrors('系统错误');
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
        if (Games::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return response()->json(['code' => 1, 'msg' => '删除失败']);
    }

    /**
     * 开启关闭游戏
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isuse(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return response()->json(['code' => 1, 'msg' => '请选择记录项']);
        }
        $user = Games::where('id', $ids)->first();
        $data['game_status'] = ($user['game_status'] == 1) ? 0 : 1;
        if ($user->whereIn('id', $ids)->update($data)) {
            return response()->json(['code' => 0, 'msg' => '操作成功']);
        }
        return response()->json(['code' => 1, 'msg' => '操作失败']);
    }

    /**
     * 开启关闭游戏玩法
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function typeIsuse(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return response()->json(['code' => 1, 'msg' => '请选择记录项']);
        }
        $user = GamesType::where('id', $ids)->first();
        $data['is_open'] = ($user['is_open'] == 1) ? 0 : 1;
        if ($user->whereIn('id', $ids)->update($data)) {
            return response()->json(['code' => 0, 'msg' => '操作成功']);
        }
        return response()->json(['code' => 1, 'msg' => '操作失败']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createType(Request $request)
    {
        $id = $request->id;
        $games = Games::where('id', $id)->first();
        $data = ['game_id' =>$id,'game_name' =>$games->game_name];
        $data = (object)$data;
        return view('admin.game.createtype', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeType(Request $request)
    {
        $data = $request->all();
        if (GamesType::create($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => '添加游戏玩法成功'
                ]);
            } else {
                return redirect()->to(route('admin.gametype'))->with(['status' => '添加游戏玩法成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.game'))->withErrors('系统错误');
        }

    }

    /**
     * 设置游戏玩法
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editType(Request $request)
    {
        $FieldName = $request->fieldname;//card_auth
        $id = $request->id;
        $sortvalue = $request->sortvalue;
        if (empty($id)) {
            return response()->json(['code' => 1, 'msg' => '请选择记录项']);
        }
        $data[$FieldName] = $sortvalue;
        if (GamesType::where('id', $id)->update($data)) {
            return response()->json(['code' => 0, 'msg' => '操作成功']);
        }else
        {
            return response()->json(['code' => 1, 'msg' => '操作失败']);
        }

    }


}
