<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends BaseModel
{
    protected $fillable = [
        'appName',
        'key' ,
        'type',
        'value',
        'sort',
    ];
    protected $rules = [
        'appNane' => 'required',
        'key' => 'required',
        'type' => 'required',
        'value' => 'required',
    ];
    use HasFactory;
}
