<?php

namespace App\Http;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'username',
        'password' ,
        'role_ids',
        'overdue_time',
        'status',
        'describe',
        'nickname',
    ];
    protected $rules = [
        'username' => 'required',
        'password' => 'required',
    ];
    protected static $initBase;
    public static function initBase(){
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
}
