<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\GroupCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroupCategoryController extends BaseController
{
    public function get(): Response
    {
        $configurations = GroupCategory::orderBy('sort', 'asc')
            ->where('is_delete', 0)
            ->paginate(10);
        return $this->success($configurations);
    }
    public function saveGroupCategory(Request $request): Response
    {
        $data = $request->all();
        // 验证信息
        $fail = GroupCategory::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        if(isset($data['id']) && $data['id'] || !GroupCategory::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = GroupCategory::getInfo([['name' , '=' , $data['name'] ] ]);
            if($info){
                return $this->error('Cannot add name with same name');
            }
        }
        $from = [
            'cat_id' => $data['cat_id'],
            'name' => $data['name'],
            'image' => $data['image'],
            'link' => $data['link'],
            'sort' => (int)$data['sort'] ?? 1,
            'is_visible' => $data['is_visible'],
        ];
        if ($data['image']) {
            $address = $this->fileUpload($data['image'], 'uploads/');
            $from['image'] = $address;
        }
        $id = GroupCategory::saveInfo($from);
        return $this->success($id);
    }
    public function updateGroupCategory(Request $request): Response
    {
        $data = $request->all();
        // 验证信息
        $fail = GroupCategory::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        $groupCat = GroupCategory::findOrFail($data['id']);
        if ($data['image']) {
            $address = $this->fileUpload($data['image'], 'uploads/');
            $groupCat->image = $address;
        }
        $groupCat->cat_id = $data['cat_id'];
        $groupCat->name = $data['name'];
        $groupCat->link = $data['link'];
        $groupCat->sort = $data['sort'];
        $groupCat->is_visible = $data['is_visible'];
        $groupCat->update();
        return $this->success($groupCat, 0, 201);
    }
    public function deleteGroupCategory(Request $request): Response
    {
        $id = $request->input('id' , null);
        GroupCategory::deleteInfo($id);
        return $this->success($id);
    }
}