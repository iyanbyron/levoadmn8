<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SetRedisController;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Site;
use Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;


class ProductController extends Controller
{
    /**
     * 定义jwt验证$guard参数
     * @var string
     */
    protected $guard = 'api';

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getuser']]);
    }

    /**
     * 产品价格列表
     *pr_type:支付类型1vip,2金币
     * */
    public function ProductList(Request $request)
    {
        $pr_type = $request->pr_type;
        if ($pr_type == "") {
            return $this->fail('参数不能为空！', "400");
        }
        $result_data_sting = "product:type_{$pr_type}";
        $orderby_desc = env('APP_NAME') . "product:content:*->pr_rank";
        //echo json_encode($orderby_desc);exit;
        $page = 1;
        $token = Auth::guard($this->guard)->getToken()->get();
        $pagesize = 20;
        $list = redis_product_page_detail($result_data_sting, $orderby_desc, $page, $pagesize);
        if ($list['current_page'] > $list['last_page']) {
            return $this->success([], $token, '', $list['current_page'], $list['last_page'], $list['total']);
        }
        return $this->success($list['data'], $token, '', $list['current_page'], $list['last_page'], $list['total']);
    }


}

