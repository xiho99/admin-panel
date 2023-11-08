<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends BaseModel
{
    use HasFactory;
    protected $table = 'configurations';
    protected $fillable = [
        'appName',
        'key' ,
        'type',
        'value',
        'sort',
        'is_delete',
    ];
    protected $rules = [
        'appName' => 'required',
        'key' => 'required',
        'type' => 'required',
//        'value' => 'required',
    ];

    protected static $initBase;
    public static function initBase(): static
    {
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
