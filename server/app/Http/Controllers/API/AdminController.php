<?php

namespace App\Http\Controllers\API;

use App\Http\Admin;
use App\Http\Controllers\BaseResponseController;
use App\Models\OperationLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class AdminController extends BaseResponseController
{
    public function login(Request $request): Response
    {
        // Verify the username and password provided by the user
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error('Username and password are required');
        }

        $credentials = $request->only('username', 'password');
        if (! $token = auth('admin')->attempt($credentials)) {
            return $this->error( 'Account password is wrong');
        }
        $user = auth('admin')->user();

        if ($user->status !== 1) {
            auth('admin')->logout();
            return $this->error('User has been disabled');
        }

        // Add log to database
        OperationLog::saveInfo([
            'controller' => 'AdminController',
            'method' => 'login',
            'parameters' => '[]',
            'start_time' => date('Y-m-d H:i:s',time()),
            'end_time' => date('Y-m-d H:i:s',time()),
            'admin_id' => $user['id'],
            'nickname' => $user['nickname'],
        ]);
        return $this->responseSuccess(['access_token' => 'Bearer '.$token,'userName'=>$user['nickname']]);
    }

    public function logout(): Response
    {
        auth('admin')->logout();

        return $this->responseSuccess(null);
    }
    public function saveAdmin(Request $request){
        $data = $request->all();
        $fail = Admin::getNotPassValidator($data);
        if($fail){
            return $this->responseFail('Username and password are required!');
        }
        if(!$data['nickname'] ){
            return $this->responseFail('Required for nickname');
        }
        if(!isset($data['id']) || !Admin::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = Admin::getInfo([['username' , '=' , $data['username'] ] ]);

            if($info){
                return $this->responseFail('Cannot add role with same name');
            }
        }
        $from = [
            'id' => $data['id'] ?? null,
            'username' => $data['username'] ?? '',
            'password' =>  bcrypt($data['password']) ?? '',
            'role_ids' =>  $data['role_ids'] ?? '',
            'overdue_time' =>  $data['overdue_time'] ?? null,
            'describe' =>  $data['describe'] ?? '',
            'status' =>  $data['status'] ?? 0,
            'nickname' =>  $data['nickname'] ?? '',
        ];
        $id = Admin::saveInfo($from);
        return $this->responseSuccess($id);
    }
}
