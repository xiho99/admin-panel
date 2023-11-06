<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BaseController extends FileHelperController
{
    public function getMsecTime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msecTime = sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return (float)$msecTime;
    }

    public function success($result, $errorCode = 0, $status = 200): Response
    {
        $response = [
            'code' => $errorCode,
            'message' => 'Success',
            'data' => $result,
            'server_time' => $this->getMsecTime(),
        ];
        return Response($response, $status);
    }

    public function error($message = 'error', $code = 1, $data = []): Response
    {
        $response = [
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'server_time' => $this->getMsecTime(),
        ];
        return Response($response);
    }

    public function createdBy()
    {
        return auth()->user()->name;
    }

    public function getUser()
    {
        return auth()->user();
    }

    public function getAuthId()
    {
        return auth()->user()->id;
    }
}

