<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class FileHelperController extends Controller
{
    public function fileUpload($file, $location) {
        if(!File::exists(public_path($location))) {
            mkdir(public_path($location), 0777, true);
        }
        if (!empty($file && strpos($file, ';'))) {
            $position = strpos($file, ';');
            $sub = substr($file, 0, $position);
            $ext = explode('/', $sub)[1];
            $imageName = '_' . time() ;
            $img = Image::make($file);
//            $img->resize(500, null, function ($constraint) {
//                $constraint->aspectRatio();
//            });
            $uploadPath = $location . $imageName . '.'. $ext;
        } else {
            $image_name = uniqid() . '.' . $file->getClientOriginalExtension();
            $img = Image::make($file);
            $uploadPath = $location . $image_name;
        }
        $img->save(public_path('storage/' . $uploadPath));
        return $uploadPath;
    }
    public function filesUpload($files, $location)
    {
        $filesAddress = [];
        if (!empty($files)) {
            foreach($files as $file){
                $position = strpos($file, ';');
                $sub = substr($file, 0, $position);
                $ext = explode('/', $sub)[1];
                $imageName = '_' . uniqid() ;
                $img = Image::make($file)->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                if(!File::exists(public_path($location))) {
                    mkdir(public_path($location), 0777, true);
                }
                $uploadPath = $location . $imageName . '.'. $ext;
                $img->save(public_path($uploadPath));
                array_push($filesAddress, $uploadPath);
            }
        }
        return $filesAddress;
    }
    public function upload($file, $location) {
        $folderPath = public_path() . '/' . $location;
        $image_parts = explode(";base64,", $file);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $uniqid = uniqid();
        $file = $folderPath . $uniqid . '.' . $image_type;
        file_put_contents($file, $image_base64);
        return $file;
    }
}
