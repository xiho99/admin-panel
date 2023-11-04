<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Role;
use Illuminate\Http\Request;
// 角色控制器
class RoleController extends BaseController
{
    // 存储角色信息
    public function saveRole(Request $request){
        $data = $request->all();
        // 验证信息
        $fail = Role::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        if(!isset($data['id']) || !Role::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = Role::getInfo([['roleName' , '=' , $data['roleName'] ] ]);
            if($info){
                return $this->error('Cannot add role with same name');
            }
        }
        $from = [
            'id' => $data['id'] ?? null,
            'roleName' => $data['roleName'] ?? '',
            'roleSign' =>  $data['roleSign'] ?? '',
            'sort' =>  $data['sort'] ?? '',
            'status' =>  $data['status'] ?? '',
            'meta' =>  $data['meta'] ?? '',
            'describe' =>  $data['describe'] ?? '',
            'menu_ids' =>  $data['menu_ids'] ?? '',
        ];
        $id = Role::saveInfo($from);
        return $this->success($id);
    }
    // 存储角色信息
    public function getList(Request $request){
        $name = $request->input('name' , null);
        $page = $request->input('page' , 1);
        $pageSize = $request->input('pageSize' , 20);
        $where = [];

        if($name){
            $where[] = ['roleName' , 'like',"%{$name}%"];
        }

        $data = Role::pageList($where,'*',$page,$pageSize);

        return $this->success($data);
    }
    // 存储角色信息
    public function getAllRole(){

        $data = Role::select('id','roleName')->get()->pluck(null, 'id');
        $data[0] = ['id' => 0 , 'roleName' => '超级管理员'];
        return $this->success($data);
    }
    // 存储角色信息
    public function deleteRole(Request $request){
        $id = $request->input('id' , null);
        Role::deleteInfo($id);
        return $this->success($id);
    }
}
