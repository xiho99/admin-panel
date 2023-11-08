<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends BaseModel
{
    use HasFactory;
    protected $table = 'menu_items';
    protected $fillable = [
        'name',
        'image',
        'link',
        'sort',
        'color',
        'type',
        'is_visible',
        'is_delete',
    ];
    protected $rules = [
        'name' => 'required',
        'type' => 'required',
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
