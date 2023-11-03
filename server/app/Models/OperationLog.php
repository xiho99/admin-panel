<?php

namespace App\Models;

class OperationLog extends BaseModel
{
    protected $fillable = ['id', 'controller', 'method', 'parameters', 'nickname', 'admin_id', 'end_time', 'start_time'];
    protected static $initBase;
    public static function initBase(): static
    {
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
