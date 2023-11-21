<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class UploadController extends Controller
{
    public function upload(Request $request): Response
    {
        // 验证规则
        $rules = [
            'file' => 'required|file|max:512000', // 1MB 最大文件大小
        ];

        $messages = [
            'file.max' => '文件大小不能超过1MB。',
            'file.required' => '请上传文件。',
            'file.file' => '无效的文件。',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($validator->fails()) {
                return Response([
                    'code' => 1,
                    'message' => 'Failed',
                    'data' => $validator->errors()->first(),
                    'server_time' => $this->getMsecTime(),
                    'version' => env('_VERSION')
                ]);
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // 验证文件类型
            $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'mp4', 'mov']; // 允许的文件类型
            $fileType = $file->getClientOriginalExtension();

            if (!in_array($fileType, $allowedTypes)) {
                return Response([
                    'code' => 1,
                    'message' => '不允许上传此类型的文件',
                    'data' => $validator->errors()->first(),
                    'server_time' => $this->getMsecTime(),
                    'version' => env('_VERSION')
                ]);
            }

            // 生成时间戳
            $t = date('Y-m-d', strtotime('today'));

            // 构建存储路径
            $path = 'uploads/';

            // 存储文件
            $file->store($path, 'public');

            // 获取上传后的文件名
            $fileName = $file->hashName();
        }
        return Response([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'path' => $path,
                'url' => $path . $fileName,
                'filename' => $fileName, // 添加文件名称到返回数组
            ],
            'server_time' => $this->getMsecTime(),
            'version' => env('_VERSION')
        ]);
    }
    public static function getMsecTime() {
        list($msec, $sec) = explode(' ', microtime());
        $msecTime = sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return (float)$msecTime;
    }
}
