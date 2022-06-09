<?php

namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * 不应该被报告的异常类型列表.
     *
     * @var array
     */
    protected $dontReport = [
        //InvalidRequestException::class,
    ];

    // 认证异常时不被flashed的数据
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

// 上报异常至错误driver，如日志文件(storage/logs/laravel.log)，第三方日志存储分析平台
    public function report($exception)
    {
        parent::report($exception);
    }

    // 将异常信息响应给客户端
    public function render($request, $exception)
    {
        try {
            // Token过期之自定义错误
            if ($exception->getMessage() == 'Unauthenticated.') {
                $url = $request->url();
                if (explode('/', parse_url($url)['path'])[1] == 'admin') {
                    return $request->expectsJson()
                        ? response()->json(['message' => $exception->getMessage()], 401)
                        : redirect()->guest(route('admin.login'));
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => 402,
                        'message' => '登录已过期',
                        'token' => '',
                        'data' => [],
                    ])->withHeaders([
                        'Content-Type' => 'application/json',
                        'api_token' => '',
                        'Authorization' => ''
                    ]);
                }
            }
            // 参数验证错误的异常，我们需要返回 400 的 http code 和一句错误信息
            if ($exception instanceof ValidationException) {
                //return response(['error' => array_key_first(array_collapse($exception->errors()))], 400);
                $url = $request->url();
                if (explode('/', parse_url($url)['path'])[1] == 'admin') {
                    return $request->expectsJson()
                        ? response()->json(['message' => $exception->getMessage()], 401)
                        : redirect()->guest(route('admin.login'));
                } else {
                    return response()->json([
                        'status' => false,
                        'code' => 403,
                        'message' => '参数验证错误',
                        'token' => '',
                        'data' => [],
                    ])->withHeaders([
                        'Content-Type' => 'application/json',
                        'api_token' => '',
                        'Authorization' => ''
                    ]);
                }
            }
            // 用户认证的异常，我们需要返回 401 的 http code 和错误信息
            if ($exception instanceof UnauthorizedHttpException) {
                return response($exception->getMessage(), 400);
            }

            // 接口请求方式不在路由设置中或不被允许时
            $json_msg = new Controller();
            if ($exception instanceof MethodNotAllowedHttpException) {
                return $json_msg->ail('Method Not Allowed！', "405");
            }

            // 404异常扩展设置
            if ($exception instanceof NotFoundHttpException) {
                return $json_msg->fail('Not Found！', "400");
            }


            /* 错误页面 */
            if ($exception instanceof HttpException) {
                $code = $exception->getStatusCode();

                if (view()->exists('errors.' . $code)) {
                    $message = $exception->getMessage();
                    return response()->view('errors.' . $exception->getStatusCode(), ['message' => $message], $exception->getStatusCode());
                }
            }

            //sql错误处理
            switch ($exception) {
                case ($exception instanceof \Illuminate\Database\QueryException):
                    return $json_msg->fail('sql error!', "400");
                    //$this->saveSqlError($exception);
                    break;
                default:
                    return $json_msg->fail('Server code error!', "400");
            }
        } catch (Exception $e) {
            $msg = '未定义错误';
            if ($exception->getCode() == 404) {
                $msg = '请求资源不存在';
            }
            if ($exception->getCode() == 500) {
                $msg = '服务器错误';
            }
            if ($exception->getCode() == 400) {
                $msg = '错误的请求（URL 或参数不正确）';
            }
            //$e->getMessage();
            return response()->json([
                'status' => '405',
                'msg' => $msg,
                'data' => [],
            ]);
        }

        return parent::render($request, $exception);

    }

    // 新添加的handle函数
    public function handle($request, Exception $e)
    {
        // 只处理自定义的APIException异常
        if ($e instanceof ApiException) {
            $result = [
                "msg" => "",
                "data" => $e->getMessage(),
                "status" => 0
            ];
            return response()->json($result);
        }
        return parent::render($request, $e);
    }

    /**
     * 获取返回sql错误参数
     * @param $exception
     * @return array
     */
    public function saveSqlError($exception)
    {
        $sql = $exception->getSql();
        $bindings = $exception->getBindings();

        // Process the query's SQL and parameters and create the exact query
        foreach ($bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else {
                if (is_string($binding)) {
                    $bindings[$i] = "'$binding'";
                }
            }
        }
        $query = str_replace(array('%', '?'), array('%%', '%s'), $sql);
        $query = vsprintf($query, $bindings);

        // Here's the part you need
        $errorInfo = $exception->errorInfo;

        $data = [
            'sql' => $query,
            'message' => isset($errorInfo[2]) ? $errorInfo[2] : '',
            'sql_state' => $errorInfo[0],
            'error_code' => $errorInfo[1]
        ];

        return $data;
    }

    /**
     * 登录过期后的跳转地址
     * @param \Illuminate\Http\Request $request
     * @param AuthenticationException $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $url = $request->url();
        if (explode('/', parse_url($url)['path'])[1] == 'admin') {
            //$url_path=route('admin.login');
            return $request->expectsJson()
                ? response()->json(['message' => $exception->getMessage()], 401)
                : redirect()->guest(route('admin.login'));
        } else {
            //$url_path="/api/token_out";
            return response()->json([
                'status' => false,
                'code' => 406,
                'message' => '登录已经过期',
                'token' => '',
                'data' => [],
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'api_token' => '',
                'Authorization' => ''
            ]);
        }
        /*return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest($url_path);*/
    }
}
