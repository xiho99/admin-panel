<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
class UploadController extends Controller
{
    public function upload(Request $request): Response
    {
        // Validation rules
        $rules = [
            'file' => 'required|file|max:1024000', // 4MB Maximum file size
        ];
        $messages = [
            'file.max' => 'File size cannot exceed 4MB。',
            'file.required' => 'Please upload files。',
            'file.file' => 'Invalid file。',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Response([
                'code' => 1,
                'message' => 'Failed',
                'data' => $validator->errors()->first(),
                'server_time' => $this->getMsecTime(),
                'version' => env('_VERSION')
            ]);
        }
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Verify file type
            $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'mp4', 'mov']; // Allowed file types
            $fileType = $file->getClientOriginalExtension();
            if (!in_array($fileType, $allowedTypes)) {
                return Response([
                    'code' => 1,
                    'message' => 'Uploading files of this type is not allowed',
                    'data' => $validator->errors()->first(),
                    'server_time' => $this->getMsecTime(),
                    'version' => env('_VERSION')
                ]);
            }
            // Generate timestamp
            $t = date('Y-m-d', strtotime('today'));
            // Build storage path
            $path = 'uploads/';
            // Store files
            $file->store($path, 'public');
            // Get the uploaded file name
            $fileName = $file->hashName();
        }
        return Response([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'path' => $path,
                'url' => $path . $fileName,
                'filename' => $fileName, // Add file names to the returned array
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
