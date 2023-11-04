<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class AdminUser extends BaseModel
{
    protected $table = 'admins';

    protected $fillable = [
        'userName',
        'password' ,
        'role_ids',
        'overdue_time',
        'status',
        'describe',
        'nickname',
    ];
    protected $rules = [
        'userName' => 'required',
        'password' => 'required',
    ];
    protected $hidden = [
        'password'
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
