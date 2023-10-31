<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $user;
    /**
     * @var int 会员ID
     */
    protected $uid = 0;

    public function __construct(Request $request) {
        $this->user =  auth('admin')->user();
        if ($this->user) {
            $this->uid = $this->user->id;
        }
    }

}
