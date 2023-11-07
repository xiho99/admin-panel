<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Ads;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdsController extends BaseController
{
    public function get(): Response {
        $where = [];
        $page = request()->input('page' , 1);
        $pageSize = request()->input('pageSize' , 10);
        $order = 'CAST(sort AS UNSIGNED) ASC';
        $configurations = Ads::getListData($where, ['*'], $page, $pageSize, $order);
        return $this->success($configurations);
    }
    public function saveAds(Request $request): Response {
        $data = $request->all();
        // 验证信息
        $fail = Ads::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        if(isset($data['id']) && $data['id'] || !Ads::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = Ads::getInfo([['title' , '=' , $data['title'] ] ]);
            if($info){
                return $this->error('Cannot add name with same name');
            }
        }
        $from = [
            'title' => $data['title'],
            'link' => $data['link'],
            'is_visible' => $data['is_visible'],
            'image' => $data['image'] ?? 1,
            'sort' => (int)$data['sort'] ?? 1,
        ];
        if ($data['image']) {
            $address = $this->fileUpload($data['image'], 'uploads/');
            $from['image'] = $address;
        }
        $id = Ads::saveInfo($from);
        return $this->success($id);
    }
    public function updateAds(Request $request): Response {
        $data = $request->all();
        // 验证信息
        $fail = Ads::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        $ads = Ads::findOrFail($data['id']);
        if ($data['image']) {
            $address = $this->fileUpload($data['image'], 'uploads/');
            $ads->image = $address;
        }
        $ads->title = $data['title'];
        $ads->link = $data['link'];
        $ads->is_visible = $data['is_visible'];
        $ads->sort = $data['sort'];
        $ads->update();
        return $this->success($ads, 0, 201);
    }
    public function deleteAds(Request $request)
    {
        $ads = Ads::findOrFail($request['id']);
        $ads->is_delete = 1;
        $ads->update();
        return $this->success(null,  0);
    }
}
