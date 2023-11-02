<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseResponseController;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConfigurationController extends BaseResponseController
{

    public function get(): Response
    {
        $configurations = Configuration::orderBy('created_at', 'desc')
            ->where('is_delete', 0)
            ->paginate(10);
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
        ];
        if ($data['type'] == 'image') {
            $address = $this->fileUpload($data['value'], 'uploads/');
            $item['value'] = $address;
        } else {
            $item['value'] = $data['value'];
        }
        $id = Configuration::saveInfo($item);
        return $this->success($id);
    }
    public function updateConfiguration(Request $request) {
        $data = $request->all();
        // 验证信息
        $fail = Configuration::getNotPassValidator($data);
        if($fail){
            return $this->error('Missing required fields');
        }
        $config = Configuration::findOrFail($data['id']);
        if ($data['type'] == 'image' && isset($data['value'])) {
            $address = $this->fileUpload($data['value'], 'uploads/');
            $config->value = $address;
        }
        if ($data['type'] === 'text' && isset($data['value'])) {
            $config->value = $data['value'];
        }
        $config->appName = $data['appName'];
        $config->key = $data['key'];
        $config->type = $data['type'];
        $config->sort = $data['sort'];
        $config->update();
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
