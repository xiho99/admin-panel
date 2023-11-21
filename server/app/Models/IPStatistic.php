<?php

namespace App\Models;

class IPStatistic extends BaseModel
{
    protected $table = 'ip_statistics';
    protected $fillable = ['id', 'ip', 'method', 'parameters', 'ip_access', 'controller', 'create_time'];

    protected static $initBase;
    public static function initBase(): static
    {
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
