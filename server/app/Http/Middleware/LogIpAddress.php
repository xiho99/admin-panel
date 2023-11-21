<?php

namespace App\Http\Middleware;

use App\Models\IPStatistic;
use Carbon\Carbon;
use Closure;

class LogIpAddress
{
    public function handle($request, Closure $next)
    {

        $clientIP = request()->ip();
        $today = date('Y-m-d');
        $response = $next($request);

        // Get controller and method names
        $controller = $request->route()->getAction('controller');
        list($controller, $method) = explode('@', $controller);
        // Operator information

        $parameters = json_encode($request->all());
        $where = [['ip', '=', $clientIP]];
        $isExist = IPStatistic::getInfo($where);
        if (!empty($isExist) && $isExist['create_time'] == $today) {
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
