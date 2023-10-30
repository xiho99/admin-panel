<?php

namespace App\Models;

class OperationLog extends BaseModel
{
    protected $guarded = [];
    protected static $initBase;
    public static function initBase(){
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
