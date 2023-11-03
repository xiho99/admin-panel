<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'name',
        'key',
        'sort',
        'is_visible',
        'is_delete',
    ];
    protected $casts = [
        'is_visible' => 'boolean',
    ];
    protected static $initBase;
    public static function initBase(){
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
