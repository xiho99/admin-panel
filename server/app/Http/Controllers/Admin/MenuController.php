<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseResponseController;
use App\Http\Controllers\Controller;
use App\Models\SearchWord;
use Illuminate\Http\Request;

class MenuController extends BaseResponseController
{
    public function saveInfo(Request $request){
        $data = $request->all();
        $from = [
            'id' => $data['id'] ?? null,
            'name' => $data['name'] ?? '',
            'is_link' =>  $data['is_link'] ?? 0,
            'link' =>  $data['link'] ?? '',
            'status' =>  $data['status'] ?? '',
        ];
        $id = SearchWord::saveInfo($from);
        return $this->success($id);
    }
    // 存储角色信息
    public function getList(Request $request){
        $name = $request->input('name' , null);
        $page = $request->input('page' , 1);
        $pageSize = $request->input('pageSize' , 20);
        $where = [];

        if($name){
            $where[] = ['name' , 'like',"%{$name}%"];
        }

        $data = SearchWord::pageList($where,'*',$page,$pageSize);

        return $this->success($data);
    }
    // 存储角色信息
    public function deleteInfo(Request $request){
        $id = $request->input('id' , null);
        SearchWord::deleteInfo($id);
        return $this->success($id);
    }
}
