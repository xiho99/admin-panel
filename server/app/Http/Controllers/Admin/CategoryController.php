<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseResponseController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends BaseResponseController
{
    public function get(): Response
    {
        $configurations = Category::orderBy('sort', 'asc')
            ->where('is_delete', 0)
            ->paginate(10);
        return $this->success($configurations);
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
