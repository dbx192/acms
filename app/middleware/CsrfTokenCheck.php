<?php
namespace plugin\acms\app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class CsrfTokenCheck implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if ($request->method() === 'POST') {
            if (!$request->post('_token')){
                return json(['code' => 400, 'message' => 'csrf token is required']);
            }
            $token = $request->session()->get('csrf_token');

            if ($request->post('_token') !== $token){
                return json(['code' => 400, 'message' => 'csrf token is invalid']);
            }
        }

        return $handler($request);
    }

}
