<?php

namespace App\Http\Middleware;

use App\Models\OperationLog;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class LogOperation
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->shouldLogOperation($request)) {
//            if ($request->ip == '1192.168.254.106') {
//                $ipdata = [
//                    'country' => '内网IP',
//                    'region' => '',
//                    'city' => ' ',
//                    'county' => '',
//                    'isp' => '内网IP',
//                    'isp_id' => 'local',
//                ];
//            } else {
//                $ipdata = $this->Get_Ip_To_City($request->ip);
//            }
            $log = [
                'user_id' => Auth::user() ? Auth::user()->id : 0,
                'path' => substr($request->path(), 0, 255),
                'method' => $request->method(),
                'ip' => $request->getClientIp(),
                'input' => json_encode($request->input()),
                'agent' => $_SERVER['HTTP_USER_AGENT'],
            ];
            $agent = new Agent();
            $agent->setUserAgent($log['agent']);
            $log['platform'] = $agent->platform() ?? null;
            $log['browser'] = $agent->browser() ?? null;

            try {
                OperationLog::create($log);
            } catch (\Exception $exception) {
                // pass
            }
        }

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function shouldLogOperation(Request $request)
    {
        return config('custom.operation_log.enable')
            && !$this->inExceptArray($request)
            && $this->inAllowedMethods($request->method());
    }


    public function Get_Ip_To_City($ip = '')
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ip = json_decode(file_get_contents($url));
        if ((string)$ip->code == '1') {
            return false;
        }
        $data = (array)$ip->data;
        return $data;
    }

    protected function inAllowedMethods($method)
    {
        $allowedMethods = collect(config('custom.operation_log.allowed_methods'))->filter();

        if ($allowedMethods->isEmpty()) {
            return true;
        }

        return $allowedMethods->map(function ($method) {
            return strtoupper($method);
        })->contains($method);
    }

    protected function inExceptArray($request)
    {
        foreach (config('custom.operation_log.except') as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            $methods = [];

            if (Str::contains($except, ':')) {
                list($methods, $except) = explode(':', $except);
                $methods = explode(',', $methods);
            }

            $methods = array_map('strtoupper', $methods);

            if ($request->is($except) &&
                (empty($methods) || in_array($request->method(), $methods))) {
                return true;
            }
        }

        return false;
    }

}
