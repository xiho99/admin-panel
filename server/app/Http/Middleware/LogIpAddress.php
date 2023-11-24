<?php

namespace App\Http\Middleware;

use App\Models\IPStatistic;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class LogIpAddress
{
    public function handle(Request $request, Closure $next)
    {
        $clientIP = $request->getClientIp();
        $today = date('Y-m-d');
        $response = $next($request);
        // Get controller and method names
        $controller = $request->route()->getAction('controller');
        list($controller, $method) = explode('@', $controller);
        // Operator information

        $parameters = json_encode($request->all());
        $orWhere = [['ip', '=', $clientIP, ['create_time', '=', $today]]];
        $where = [['', 'or', $orWhere]];
        $isExist = IPStatistic::getInfo($where);
        if (!empty($isExist)) {
            IPStatistic::saveInfo([
                'id' => $isExist['id'],
                'ip' => $isExist['ip'],
                'ip_access' => $isExist['ip_access'] + 1,
                'controller' => $isExist['controller'],
                'method' => $isExist['method'],
                'parameters' => $isExist['parameters'],
            ]);
        } else {
            // Add log to database
            IPStatistic::saveInfo([
                'ip' => $clientIP,
                'ip_access' => 1,
                'controller' => $controller,
                'method' => $method,
                'parameters' => $parameters,
                'create_time' => $today,
            ]);
        }

        return $response;
    }
}
