<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\MenuIcon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuIconController extends BaseController
{
    public function get(Request $request): Response
    {
        //todo: Redis
        $where = [];
        $page = $request->input('page' , 1);
        $pageSize = $request->input('pageSize' , 20);
        $order = 'CAST(sort AS UNSIGNED) ASC';
        $menuItems = MenuIcon::getListData($where, ['*'], $page, $pageSize, $order);
        return $this->success($menuItems);
    }
    public function saveMenuIcon(Request $request): Response {
        $data = $request->all();
        // verify message
        $fail = MenuIcon::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        if(isset($data['id']) && $data['id'] || !MenuIcon::getInfo([['id' , '=' , $data['id'] ] ])){
            $self = MenuIcon::getInfo(['id' => $data['id']]);
            $info = MenuIcon::getInfo([['name' , '=' , $data['name'] ] ]);
            if($info && $info['id'] != $self['id']) {
                return $this->error('Name already exist!');
            }
        }

        $from = [
            'id' => $data['id'] ?? null,
            'name' => $data['name'],
            'link' => $data['link'],
            'is_visible' => $data['is_visible'],
            'sort' => (int)$data['sort'] ?? 1,
        ];
        if ($data['image']) {
            $address = $this->fileUpload($data['image'], 'uploads/');
            $from['image'] = $address;
        }
        $id = MenuIcon::saveInfo($from);
        return $this->success($id);
    }

    public function deleteMenuIcon(Request $request)
    {
        $id = $request->input('id' , null);
        MenuIcon::deleteInfo($id);
        return $this->success($id);
    }
}
