<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = ['menuSuperior','menuSuperiorPath' , 'menuType', 'name', 'component', 'isLink', 'menuSort', 'redirect', 'path', 'meta','is_parent'];
    protected $rules = [
        'name' => 'required',
        'path' => 'required',
        'meta' => 'required',
    ];
    protected static $initBase;
    public static function initBase(){
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
