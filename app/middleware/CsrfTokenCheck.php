<?php

namespace plugin\acms\app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class CsrfTokenCheck implements MiddlewareInterface
{
    /**
     * 排除的应用
     * @var array
     */
    protected $excludedApps = [];

    /**
     * 构造函数
     */
    public function __construct($excludedApps = [])
    {
        $this->excludedApps = $excludedApps;
    }
    // 需要验证 CSRF 的 HTTP 方法
    protected $methodsToVerify = ['POST', 'PUT', 'PATCH', 'DELETE'];

    // 在 CsrfTokenCheck 中间件中添加：
    public function process(Request $request, callable $handler): Response
    {
        // 当前请求的应用属于排除列表，则忽略
        if (in_array($request->app, $this->excludedApps)) {
            return $handler($request);
        }
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $request->header('X-CSRF-TOKEN') ?? '';
            $sessionToken = $request->session()->get('csrf_token');

            if (empty($token) || !hash_equals($sessionToken, $token)) {
                return json(['code' => 419, 'msg' => 'CSRF token invalid'], 320);
            }
        }

        return $handler($request);
    }
}
