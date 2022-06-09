<?php
/**
 * token验证
 */

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use  Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Http\Controllers\Controller;
use App\Models\Member;

// 注意，我们要继承的是 jwt 的 BaseMiddleware
class AutoToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     */
    protected $guard = 'api';

    public function handle($request, Closure $next)
    {
        /*$response = $next($request);
        $response->header('Access-Control-Allow-Origin', 'http://192.168.0.xxx:xxx','	http://local.my3158.com','http://114.1xx.129.xx:xxx');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, multipart/form-data, application/json, Authorization');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'false');*/
        $this->checkForToken($request);
        $token = Request()->header('Authorization');
        if (!(substr($token, 0, 6) == "Bearer" || substr($token, 0, 6) == "bearer")) {
            return Controller::fail('无参数token', 400);
        }
        if ($token == null)
            return Controller::fail('无参数token', 400);
        // 捕捉token过期所抛出的 TokenExpiredException异常
        try {
            // 检测用户的登录状态，如果正常则通过
            if (JWTAuth::parseToken()->authenticate()) {
                return $next($request);
            }
            throw new UnauthorizedHttpException('jwt-auth', '未登录');
        } catch (TokenInvalidException $e) {
            return Controller::fail('token无效', 400);
        } catch (TokenExpiredException $exception) {
            //token过期所抛出异常，刷新该用户的token并将它添加到响应头中
            try {
                // 刷新用户的 token
                $newToken = Auth::guard($this->guard)->refresh();
                // 使用一次性登录以保证此次请求的成功
                Auth::guard($this->guard)->onceUsingId(Auth::guard($this->guard)->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                $user = Auth::guard($this->guard)->user();
		if ($user['status'] == 0) {
			return Controller::fail('用户不存在', 401);
		}
                if (!$user) {
                    return Controller::fail('用户不存在', 400);
                } else {
                    $access_token = 'Bearer ' . $newToken;
                    request()->headers->set('Authorization', $access_token);
                    return Controller::fail($exception->getMessage(), 410, $newToken, $user);
                }
            } catch (JWTException $exception) {
                // 如果异常，refresh 也过期了，用户无法刷新令牌，需要重新登录。
                // 过期用户
                return Controller::fail($exception->getMessage(), 400);
            }
        }
        // 在响应头中返回新的token
        return $this->setAuthenticationHeader($next($request), $newToken);
    }
}

