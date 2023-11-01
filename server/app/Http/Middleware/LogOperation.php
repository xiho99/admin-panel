<?php

namespace App\Http\Middleware;

use App\Models\OperationLog;
use Closure;

class LogOperation
{
    public function handle($request, Closure $next)
    {
        // 执行操作前记录时间
        $startTime = now();

        $response = $next($request);

        // 执行操作后记录时间
        $endTime = now();

        // 获取控制器和方法名称
        $controller = $request->route()->getAction('controller');
        list($controller, $method) = explode('@', $controller);
        // 操作人信息
        $user =  auth('admin')->user();

        // 获取请求参数
        $parameters = json_encode($request->all());

        // 添加日志到数据库
        OperationLog::saveInfo([
            'controller' => $controller,
            'method' => $method,
            'parameters' => $parameters,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'admin_id' => $user['id'],
            'nickname' => $user['nickname'],
        ]);

        return $response;
    }
}
