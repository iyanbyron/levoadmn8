<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 处理权限分类
     */
    public function tree($list = [], $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0)
    {
        if (empty($list)) {
            $list = Permission::get()->toArray();
        }
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * api接口成功参数返回
     * @param array $data
     * @param string $token
     * @param string $message
     * @param string $current_page
     * @param string $last_page
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = [], $token = '', $message = '', $current_page = '', $last_page = '', $total = '', $cdata = [])
    {
        if (trim($token) <> "" && strpos($token, 'Bearer') === false) $token = "Bearer " . $token;
        $json_data = [
            'status' => true,
            'code' => 200,
            'message' => $message,
            //'message' => $message ? $message : config('errorcode.code')[200],
            'token' => $token,
            'current_page' => $current_page,
            'last_page' => $last_page,
            'total' => $total,
            'data' => $data,
        ];
        if ($cdata) {
            $json_data = array_merge($json_data, $cdata);
        }
        if ($current_page && $last_page) {
            return response()->json($json_data)->withHeaders([
                'Content-Type' => 'application/json',
                'api_token' => $token,
                'Authorization' => $token
            ]);
        } else {
            return response()->json([
                'status' => true,
                'code' => 200,
                //'message' => $message ? $message : config('errorcode.code')[200],
                'message' => $message,
                'token' => $token,
                'data' => $data,
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'api_token' => $token,
                'Authorization' => $token
            ]);
        }
    }

    /**
     * api接口失败参数返回
     * @param string $message
     * @param int $code
     * @param string $token
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail($message = '', $code = 400, $token = '', $data = [])
    {
        if (trim($token) <> "" && strpos($token, 'Bearer') === false) $token = "Bearer " . $token;
        return response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message ? $message : config('errorcode.code')[(int)$code],
            'token' => $token,
            'data' => $data,
        ])->withHeaders([
            'Content-Type' => 'application/json',
            'api_token' => $token,
            'Authorization' => $token
        ]);
    }

    /**
     * 验证是否获取返回header头token,默认不验证
     * @param bool $inputtoken 是否验证header
     * @return VerifiyPost|array|null|string
     */
    static function PostToken($inputtoken = false)
    {
        if ($inputtoken) {
            if (is_null($token = request()->header('authorization'))) {
                return 400;
            } else {
                $token = 'Bearer ' . $token;
            }
        } else {
            $token = "";
        }

        return $token;
    }

}
