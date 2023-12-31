<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConfigurationController extends BaseController
{

    public function get(Request $request): Response
    {
        //todo: Redis
        $where = [];
        $page = $request->input('currentPage' , 1);
        $pageSize = $request->input('pageSize' , 10);
        $order = 'CAST(sort AS UNSIGNED) ASC';
        $configurations = Configuration::getListData($where, ['*'], $page, $pageSize, $order);

//        $configurations = Configuration::orderByRaw($order)
//            ->where('is_delete', 0)
//            ->paginate($pageSize);
        return $this->success($configurations);
    }
    public function saveConfiguration(Request $request)
    {
        $data = $request->all();
        // 验证信息
        $fail = Configuration::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        if(isset($data['id']) && $data['id'] || !Configuration::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = Configuration::getInfo([['key' , '=' , $data['key'] ] ]);
            if($info){
                return $this->error('Cannot add key with same name');
            }
        }
        $item = [
            'appName' => $data['appName'],
            'key' => $data['key'],
            'type' => $data['type'] ?? 1,
            'sort' => (int)$data['sort'] ?? 1,
            'is_visible' => $data['is_visible'],
            'link' => $data['link'],
        ];
        $item['value'] = $data['value'];

        $id = Configuration::saveInfo($item);
        return $this->success($id);
    }
    public function updateConfiguration(Request $request) {
        $data = $request->all();
        $fail = Configuration::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        $config = Configuration::findOrFail($data['id']);
        if ($data['type'] == 'image' && isset($data['value'])) {
            $config->value = $data['value'];
        }
        if ($data['type'] === 'colorPicker' && isset($data['value'])) {
            $config->value = $data['value'];
        }
        if ($data['type'] === 'text' && isset($data['value'])) {
            $config->value = $data['value'];
        }
        if ($data['type'] === 'editor' && isset($data['value'])) {
            $config->value = $data['value'];
        }

        $config->appName = $data['appName'];
        $config->key = $data['key'];
        $config->type = $data['type'];
        $config->sort = $data['sort'];
        $config->link = $data['link'];
        $config->is_visible = $data['is_visible'];
        Configuration::updateCacheData($config);
        return $this->success($config, 0, 201);
    }

    public function deleteConfiguration(Request $request)
    {
        $category = Configuration::findOrFail($request->id);
        $category->is_delete = 1;
        $category->update();
        return $this->success(null,  0);
    }
}
