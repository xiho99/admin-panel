<?php

namespace App\Models;

class SearchWord extends BaseModel
{
    protected $table = 'search_words';
    protected $fillable = ['id', 'name'];
    protected static $initBase;
    public static function initBase(){
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
