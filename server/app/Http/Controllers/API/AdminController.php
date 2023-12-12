<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\Admin;
use App\Models\AdminUser;
use App\Models\OperationLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','saveAdmin']]);
    }
    public function login(Request $request): Response
    {
        // Verify the username and password provided by the user
        $validator = Validator::make($request->all(), [
            'userName' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error('Username and password are required');
        }
        $credentials = $request->only('userName', 'password');
        if (!$token = auth('admin')->attempt($credentials)) {
            return $this->error( 'Account password is wrong');
        }
        $user = auth('admin')->user();
        if ($user->status == 0) {
            auth('admin')->logout();
            return $this->error('User has been disabled');
        }
        // Add log to database
        $from = [
            'controller' => 'AdminController',
            'method' => 'login',
            'parameters' => '[]',
            'start_time' => date('Y-m-d H:i:s',time()),
            'end_time' => date('Y-m-d H:i:s',time()),
            'admin_id' => $user['id'],
            'nickname' => $user['nickname'],
        ];
        OperationLog::saveInfo($from);

        return $this->success(['access_token' => 'Bearer '.$token,'userName'=>$user['nickname']]);
    }

    public function logout(): Response
    {
        auth('admin')->logout();

        return $this->success(null);
    }
    public function saveAdmin(Request $request){
        $data = $request->all();
        $fail = AdminUser::getNotPassValidator($data);
        if($fail){
            return $this->error('Username and password are required!');
        }
        if(!$data['nickname'] ){
            return $this->error('Required for nickname');
        }
        if(!isset($data['id']) || !AdminUser::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = AdminUser::getInfo([['userName' , '=' , $data['userName'] ] ]);

            if($info){
                return $this->error('Cannot add role with same name');
            }
        }
        $from = [
            'id' => $data['id'] ?? null,
            'userName' => $data['userName'] ?? '',
            'password' =>  bcrypt($data['password']) ?? '',
            'role_ids' =>  $data['role_ids'] ?? '',
            'overdue_time' =>  $data['overdue_time'] ?? null,
            'describe' =>  $data['describe'] ?? '',
            'status' =>  $data['status'] ?? 0,
            'nickname' =>  $data['nickname'] ?? '',
        ];
        $id = AdminUser::saveInfo($from);
        return $this->success($id);
    }
}
