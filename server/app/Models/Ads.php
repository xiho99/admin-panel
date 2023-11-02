<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends BaseModel
{
    use HasFactory;
    protected static $initBase;
    protected $fillable = [
        'title',
        'link' ,
        'image',
        'sort',
        'is_delete',
    ];
    protected $rules = [
        'title' => 'required',
        'link' => 'required',
        'image' => 'required',
    ];
    public static function initBase(): static
    {
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
