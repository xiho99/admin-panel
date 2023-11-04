<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuItemController extends BaseController
{
    public function get(): Response {
        $configurations = MenuItem::orderBy('sort', 'asc')
            ->where('is_delete', 0)
            ->paginate(10);
        return $this->success($configurations);
    }
    public function saveMenuItem(Request $request): Response {
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
            'color' => $data['color'],
            'is_visible' => $data['is_visible'],
            'sort' => (int)$data['sort'] ?? 1,
        ];
        if ($data['image']) {
            $address = $this->fileUpload($data['image'], 'uploads/');
            $item['image'] = $address;
        }
        $id = MenuItem::saveInfo($item);
        return $this->success($id);
    }
    public function updateMenuItem(Request $request) {
        $data = $request->all();
        // 验证信息
        $fail = MenuItem::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        $menuItem = MenuItem::findOrFail($data['id']);
        if ($data['image']) {
            $address = $this->fileUpload($data['image'], 'uploads/');
            $menuItem->image = $address;
        }
        $menuItem->name = $data['name'];
        $menuItem->link = $data['link'];
        $menuItem->type = $data['type'];
        $menuItem->color = $data['color'];
        $menuItem->is_visible = $data['is_visible'];
        $menuItem->sort = $data['sort'];
        $menuItem->update();
        return $this->success($menuItem, 0, 201);
    }

    public function deleteMenuItem(Request $request)
    {
        $id = $request->input('id' , null);
        MenuItem::deleteInfo($id);
        return $this->success($id);
    }
}
