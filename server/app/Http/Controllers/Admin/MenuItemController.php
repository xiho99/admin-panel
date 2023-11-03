<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseResponseController;
use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuItemController extends BaseResponseController
{
    public function get(): Response {
        $configurations = MenuItem::orderBy('created_at', 'desc')
            ->where('is_delete', 0)
            ->paginate(10);
        return $this->success($configurations);
    }
    public function saveMenuCategory(Request $request): Response {
        $data = $request->all();
        // 验证信息
        $fail = MenuItem::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        if(isset($data['id']) && $data['id'] || !MenuItem::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = MenuItem::getInfo([['name' , '=' , $data['name'] ] ]);
            if($info){
                return $this->error('Cannot add name with same name');
            }
        }
        $item = [
            'name' => $data['name'],
            'link' => $data['link'],
            'type' => $data['type'],
            'sort' => (int)$data['sort'] ?? 1,
        ];
        if ($data['type'] == 'image') {
            $address = $this->fileUpload($data['value'], 'uploads/');
            $item['image'] = $address;
        } else {
            $item['image'] = $data['image'];
        }
        $id = MenuItem::saveInfo($item);
        return $this->success($id);
    }
    public function updateMenuCategory(Request $request) {
        $data = $request->all();
        // 验证信息
        $fail = MenuItem::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        $menuCat = MenuItem::findOrFail($data['id']);
        if ($data['image']) {
            $address = $this->fileUpload($data['image'], 'uploads/');
            $menuCat->image = $address;
        }
        $menuCat->name = $data['name'];
        $menuCat->link = $data['link'];
        $menuCat->type = $data['type'];
        $menuCat->sort = $data['sort'];
        $menuCat->update();
        return $this->success($menuCat, 0, 201);
    }

    public function deleteMenuCategory(Request $request)
    {
        $id = $request->input('id' , null);
        MenuItem::deleteInfo($id);
        return $this->success($id);
    }
}
