<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;

class SecretMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (count($request->input()) > 0) {
            $input_prar = $request->input();
            if (array_key_exists('param', $input_prar)) {
                $content_json = decryptWithOpenssl($input_prar['param'], config("public.app.AppKey"), config("public.app.AppIV"));
                $content_arr = json_decode($content_json, true);
                unset($request['param']);
                if (empty($content_arr)) {
                    $msg = new Controller();
                    return $msg->fail('解密失败,请核对key!');
                } else {
                    $request->merge($content_arr);
                }

            } else {
                return $next($request);
            }

        }
        $response = $next($request);
        # 拿到需要返回的数据，然后进行加密
        $content = $response->getContent();
        if ($content) {
            //$content = json_decode($content, true);
            # 对 content 进行加密处理
            //$response->setContent(json_encode($content));
            $response->setContent(encryptWithOpenssl($content, config("public.app.AppKey"), config("public.app.AppIV")));
        }
        return $response;
    }
}
