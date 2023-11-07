<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class CategoryController extends BaseController
{
    public function get(Request $request): Response
    {
        $where = [];
        $page = $request->input('currentPage' , 1);
        $pageSize = $request->input('pageSize' , 10);
        $order = 'CAST(sort AS UNSIGNED) ASC';
        $category = Category::getListData($where, ['*'],$page, $pageSize, $order);
        return $this->success($category);
    }

    public function saveCategory(Request $request): Response
    {
        $data = $request->all();
        // 验证信息
        $fail = Category::getNotPassValidator($data);
        if ($fail) {
            return $this->error('Missing required fields');
        }
        if (isset($data['id']) && $data['id'] || !Category::getInfo([['id', '=', $data['id']]])) {
            $info = Category::getInfo([['key', '=', $data['key']]]);
            if ($info) {
                return $this->error('Cannot add name with same name');
            }
        }
        $from = [
            'name' => $data['name'],
            'key' => $data['key'],
            'is_visible' => $data['is_visible'],
            'sort' => (int)$data['sort'] ?? 1,
        ];
        $id = Category::saveInfo($from);
        return $this->success($id);
    }

    public function updateCategory(Request $request): Response
    {
        $data = $request->all();
        $fail = Category::getNotPassValidator($data);
        if ($fail) {
            return $this->error('Missing required fields');
        }
        $ads = Category::findOrFail($data['id']);
        $ads->name = $data['name'];
        $ads->key = $data['key'];
        $ads->is_visible = $data['is_visible'];
        $ads->sort = $data['sort'];
        $ads->update();
        return $this->success($ads, 0, 201);
    }

    public function deleteCategory(Request $request): Response
    {
        $id = $request->input('id' , null);
        Category::deleteInfo($id);
        return $this->success($id);
    }
}
