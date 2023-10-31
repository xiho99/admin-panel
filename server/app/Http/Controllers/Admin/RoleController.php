<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseResponseController;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
// 角色控制器
class RoleController extends BaseResponseController
{
    // 存储角色信息
    function saveRole(Request $request){
        $data = $request->all();
        // 验证信息
        $fail = Role::getNotPassValidator($data);
        if($fail){
            return $this->error('缺少必填字段');
        }
        if(!isset($data['id']) || !Role::getInfo([['id' , '=' , $data['id'] ] ])){

            $info = Role::getInfo([['roleName' , '=' , $data['roleName'] ] ]);
            if($info){
                return $this->error('无法添加相同名称的角色');
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
    function getList(Request $request){
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
    function getAllRole(){

        $data = Role::select('id','roleName')->get()->pluck(null, 'id');
        $data[0] = ['id' => 0 , 'roleName' => '超级管理员'];
        return $this->success($data);
    }
    // 存储角色信息
    function deleteRole(Request $request){
        $id = $request->input('id' , null);
        Role::deleteInfo($id);
        return $this->success($id);
    }
}
