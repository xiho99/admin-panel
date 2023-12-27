<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\MenuButton;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuButtonController extends BaseController
{
    public function get(Request $request): Response
    {
        //todo: Redis
        $where = [];
        $page = $request->input('page' , 1);
        $pageSize = $request->input('pageSize' , 20);
        $order = 'CAST(sort AS UNSIGNED) ASC';
        $menuItems = MenuButton::getListData($where, ['*'], $page, $pageSize, $order);
        return $this->success($menuItems);
    }
    public function saveMenuButton(Request $request): Response {
        $data = $request->all();
        // verify message
        $fail = MenuButton::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        if(isset($data['id']) && $data['id'] || !MenuButton::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = MenuButton::getInfo([['name' , '=' , $data['name'] ] ]);
            if($info && $info['id'] != $data['id']) {
                return $this->error('Cannot add name with same name');
            }
        }
        $from = [
            'id' => $data['id'] ?? null,
            'name' => $data['name'],
            'link' => $data['link'],
            'color' => $data['color'],
            'is_visible' => $data['is_visible'],
            'sort' => (int)$data['sort'] ?? 1,
        ];

        $id = MenuButton::saveInfo($from);
        return $this->success($id);
    }
    public function deleteMenuButton(Request $request)
    {
        $id = $request->input('id' , null);
        MenuButton::deleteInfo($id);
        return $this->success($id);
    }
}
