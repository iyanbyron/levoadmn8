<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.set.info');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * Author: phpstorm
     * Date: 2019/11/2 20:41
     */
    public function setinfo(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|min:4|max:14',
            'name' => 'required',
        ]);
        $user = auth()->user();
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        if ($user->save()) {
            return redirect()->to(route('admin.set.index'))->with(['status' => '保存成功']);
        }
        return redirect()->to(route('admin.set.index'))->withErrors('系统错误');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * Author: phpstorm
     * Date: 2019/9/7 16:08
     */
    public function password()
    {
        return view('admin.set.password');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * Author: phpstorm
     * Date: 2019/9/7 18:00
     */
    public function setpassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'old_password.required' => '原始密码不能为空',
            'password.required' => '新密码不能为空',
            'password.min' => '新密码不能少于6个字符',
            'password.confirmed' => '两次输入新密码不符',
        ]);
        if ($request->get('old_password') == $request->get('password')) {
            return redirect()->to(route('admin.set.password'))->withErrors('不能使用原始密码');
        }
        $user = auth()->user();
        if (\Hash::check($request->get('old_password'), auth()->user()->password)) {
            $user->password = bcrypt($request->get('password'));
            $user->save();
            return redirect()->to(route('admin.set.password'))->with(['status' => '保存成功']);
        }
        return redirect()->to(route('admin.set.password'))->withErrors('系统错误');

    }
}
