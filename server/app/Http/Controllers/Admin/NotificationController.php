<?php

namespace App\Http\Controllers\Admin;

use App\Events\SendNotification;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Pusher\PusherException;

class NotificationController extends BaseController
{
    /**
     * @throws PusherException
     * @throws GuzzleException
     * @throws ApiErrorException
     */
    public function sendPusher(): Response
    {
        $msg = 'new booking!';
        $options = [
            'cluster' => 'ap1',
            'useTLS' => false
        ];
        if($this->createdByAdmin() === 'admin') {
            $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), $options);
            $pusher->trigger('notification-channel-' . $this->user['userName'], 'notification-event-' . $this->user['userName'], ['message' => $msg]);
        }
        return Response(false);
    }
}
