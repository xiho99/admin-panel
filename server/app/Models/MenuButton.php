<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuButton extends BaseModel
{
    use HasFactory;
    protected $table = 'menu_buttons';
    protected $fillable = [
        'name',
        'link',
        'sort',
        'color',
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
