<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseResponseController;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;

class MenuController extends BaseResponseController
{
    // 存储角色信息
    public function saveMenu(Request $request) {

        $data = $request->all();
        // 验证信息
        $fail = Menu::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        if(!isset($data['id']) || !Menu::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = Menu::getInfo([['name' , '=' , $data['name'] ] ]);
            if($info){
                return $this->error('Unable to add route with same name');
            }
        }
        $data['component'] = $data['path'] ? $data['path'].'/index' : '';
        $from = [
            'id' => $data['id'] ?? null,
            'name' => $data['name'],
            'path' =>  $data['path'],
            'meta' =>  $data['meta'],
            'menuType' => $data['menuType'] == 'menu' ? 1:0,
            'component' =>  $data['component'] ?? '',
            'isLink' =>  $data['isLink'] ? 1 : 0,
            'menuSort' =>  $data['menuSort'] ?? 1,
            'redirect' =>  $data['redirect'] ?? '',
            'menuSuperior' => '',
            'menuSuperiorPath' => '',
        ];
        if(!empty($data['menuSuperiorPath']) && $data['menuSuperiorPath']){
            $from['menuSuperior'] = $data['menuSuperiorPath'][count($data['menuSuperiorPath']) - 1];
            $from['menuSuperiorPath'] = implode(',', $data['menuSuperiorPath']);
        }
        // 给他的父级加个标记
        if($from['menuSuperior']){
            $info = Menu::getInfo([['name' , '=' , $from['menuSuperior'] ] ]);
            $info['is_parent'] = 1;
            Menu::saveInfo($info);
        }
        $id = Menu::saveInfo($from);
        return $this->success($id);
    }
    // 存储角色信息
    public function deleteMenu(Request $request){
        $id = $request->input('id' , null);
        $info = Menu::getInfo([['id' , '=' , $id ]]);
        if(!$info) return $this->error();
        Menu::deleteInfo($id);
        // 查看他的父级还有没有子级
        if($info['menuSuperior']){
            $par = Menu::getInfo([['menuSuperior' , '=' , $info['menuSuperior'] ] ]);
            if(!$par){
                $parent = Menu::getInfo([['name' , '=' , $info['menuSuperior'] ] ]);
                $parent['is_parent'] = 0;
                $id = Menu::saveInfo($parent);
            }
        }
        return $this->success($id);
    }
    // 存储角色信息
    public function adminMenu(Request $request){
        // 超级管理员
        $roleId = $this->user->role_ids;
        if($roleId == 0) {
            $list = Menu::getList([],['*'],'menuSort asc');
        }
        else {
            $menuIds = [];
            $roles = [];
            $data = Role::getForRoleid($this->user->role_ids);
            if(!$data) return $this->error($this->user->role_ids);
            foreach ($data as $v){
                $roles[] = [
                    'id' => $v['id'],
                    'roleName' => $v['roleName'],
                ];
                $menuIds = array_merge($menuIds, explode(',',$v['menu_ids']));
            }
            $list1 =  Menu::getList([['id' , 'in' , implode(',',$menuIds)]],'*','menuSort asc');
            $list2 =  Menu::getList([['is_parent' , '=' , 1]],'*','menuSort asc');
            $list = [];
            foreach ($list1 as $item) {
                $list[$item['id']] = $item;
            }

            // 父级路由
            $menuSuperiors = array_column($list1, 'menuSuperior');
            foreach ($list2 as $key => $item) {
                if(in_array($item['name'] , $menuSuperiors)){
                    $list[$item['id']] = $item;
                }
            }

            // 使用usort自定义排序规则
            usort($list, function($a, $b) {
                return $a['menuSort'] - $b['menuSort'];
            });


        }

        foreach ($list as &$v){
            $v['meta'] = json_decode($v['meta'] , true);
            $v['menuSuperiorPath'] = explode(',', $v['menuSuperiorPath']);
            $v['isLink'] = $v['isLink'] ? true : false;
            $v['menuType'] = $v['menuType'] ? 'menu' : 'btn';
        }
        return $this->success($list);
    }
}
