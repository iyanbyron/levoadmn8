<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleCreateRequest $request)
    {
        $data = $request->only(['name', 'display_name']);
        if (Role::create($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'noRefresh' => false,
                    'message' => '添加角色成功'
                ]);
            } else {
                return redirect()->to(route('admin.role'))->with(['status' => '添加角色成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.role'))->with(['status' => '系统错误']);
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
        $role = Role::findOrFail($id);//->toArray()
        //print_r(compact('role'));
        return view('admin.role.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $data = $request->only(['name', 'display_name']);
        if ($role->update($data)) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'noRefresh' => false,
                    'message' => '更新角色成功'
                ]);
            } else {
                return redirect()->to(route('admin.role'))->with(['status' => '更新角色成功']);
            }
        }
        if (request()->ajax()) {
            return response()->json([
                'status' => 'fail',
                'message' => '系统错误'
            ]);
        } else {
            return redirect()->to(route('admin.role'))->withErrors('系统错误');
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
        if (Role::destroy($ids)) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return response()->json(['code' => 1, 'msg' => '删除失败']);
    }

    /**
     * 分配权限
     */
    public function permission(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = $this->tree();
        $newarr = [];
        foreach ($permissions as $key1 => $item1) {
            $permissions[$key1]['own'] = $role->hasPermissionTo($item1['id']) ? 'checked' : false;
            /*$newarr[$key1] = [
                'id' => $item1['id'],
                'title' => $item1['display_name'],
                'field' => 'permissions[]',
                'checked' => $role->hasPermissionTo($item1['id']) ? true : false,
                'spread' => true,
                'children' => $item1['_child']
            ];*/
            if (isset($item1['_child'])) {
                foreach ($item1['_child'] as $key2 => $item2) {
                    $permissions[$key1]['_child'][$key2]['own'] = $role->hasPermissionTo($item2['id']) ? 'checked' : false;
                    /*$newarr[$key1]['children'][$key2] = [
                        'id' => $item2['id'],
                        'title' => $item2['display_name'],
                        'field' => 'permissions[]',
                        'checked' => $role->hasPermissionTo($item2['id']) ? true : false,
                        'spread' => true,
                        'children' => $item2['_child']??[]
                    ];*/
                    if (isset($item2['_child'])) {
                        foreach ($item2['_child'] as $key3 => $item3) {
                            $permissions[$key1]['_child'][$key2]['_child'][$key3]['own'] = $role->hasPermissionTo($item3['id']) ? 'checked' : false;
                            /*$newarr[$key1]['children'][$key2]['children'][$key3] = [
                                'id' => $item3['id'],
                                'title' => $item3['display_name'],
                                'field' => 'permissions[]',
                                'checked' => $role->hasPermissionTo($item3['id']) ? true : false,
                                'spread' => false,
                                'children' => $item3['_child']??[]
                            ];*/
                        }
                    }
                }
            }

        }

        return view('admin.role.permission', compact('role', 'permissions', 'newarr'));
    }

    /**
     * 存储权限
     */
    public function assignPermission(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = $request->get('permissions');

        if (empty($permissions)) {
            $role->permissions()->detach();
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'noRefresh' => true,
                    'message' => '已更新角色权限'
                ]);
            } else {
                return redirect()->to(route('admin.role'))->with(['status' => '已更新角色权限']);
            }
        }
        $role->syncPermissions($permissions);
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'noRefresh' => true,
                'message' => '已更新角色权限'
            ]);
        } else {
            return redirect()->to(route('admin.role'))->with(['status' => '已更新角色权限']);
        }
    }

}
