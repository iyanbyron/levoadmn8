<?php

namespace App\Http\Controllers\Api;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Videobigclass;
use App\Models\Navigationsmallclass;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;

$aa = new TestController;
$aa->test();

class TestController
{
    /**
     * 定义jwt验证$guard参数
     * @var string
     */
    protected $guard = 'api';

    //public function __construct()
    public function test()
    {
        //$this->middleware('auth:api', ['except' => ['StartAds']]);
        $navi_ads = Ads::get()->toArray();
        echo json_encode($navi_ads);
    }


}

