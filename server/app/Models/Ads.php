<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends BaseModel
{
    use HasFactory;
    protected $table = 'ads';
    protected static $initBase;
    protected $fillable = [
        'title',
        'link' ,
        'image',
        'sort',
        'is_visible',
        'is_delete',
    ];
    protected $rules = [
        'title' => 'required',
        'link' => 'required',
    ];
    protected $casts = [
        'is_visible' => 'boolean',
    ];
    public static function initBase(): static
    {
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
