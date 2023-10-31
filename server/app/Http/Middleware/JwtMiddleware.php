<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => '请重新登录','code' => -10004]);
        }

        // 如果令牌有效，将用户对象添加到请求中，以供后续处理
        $request->attributes->add(['user' => $user]);

        return $next($request);
    }
}

