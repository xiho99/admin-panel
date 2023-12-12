<?php

namespace App\Models;

class Role extends BaseModel
{
    protected $table = 'roles';
    protected $fillable = ['roleName', 'roleSign', 'sort', 'status', 'describe', 'menu_ids'];
    protected $rules = [
        'roleName' => 'required',
        'roleSign' => 'required',
        'menu_ids' => 'required',
    ];

    public static function getForRoleid($ids){
        if(!$ids) return ;
        $where = [['id' , 'in' , $ids]];
        return self::getList($where);
    }
    protected static $initBase;
    public static function initBase(): static
    {
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
