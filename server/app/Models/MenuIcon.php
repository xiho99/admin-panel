<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuIcon extends BaseModel
{
    use HasFactory;
    protected $table = 'menu_icons';
    protected $fillable = [
        'name',
        'image',
        'link',
        'sort',
        'is_visible',
        'is_delete',
    ];
    protected $rules = [
        'name' => 'required',
        'sort' => 'required',
        'link' => 'required',
    ];
    protected $casts = [
        'is_visible' => 'boolean',
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
