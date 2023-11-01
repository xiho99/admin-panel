<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseResponseController;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends BaseResponseController
{

    public function index()
    {
        //
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
            'id' => $data['id'] ?? null,
            'appName' => $data['appName'],
            'key' => $data['key'],
            'type' => $data['type'] ?? 1,
            'sort' => (int)$data['sort'] ?? 1,
        ];
        $id = Configuration::saveInfo($item);
        return $this->success($id);
    }

    public function destroy(Configuration $configuration)
    {
        //
    }
}
