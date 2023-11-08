<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupCategory extends BaseModel
{
    use HasFactory;
    protected $table = 'group_categories';
    protected $fillable = [
        'cat_id',
        'name',
        'link',
        'image',
        'is_visible',
        'is_delete',
        'sort',
    ];
    protected $casts = [
        'is_visible' => 'boolean',
    ];
    protected static $initBase;
    public static function initBase(){
        if(!self::$initBase){
            self::$initBase = new static();
        }
        return self::$initBase;
    }
    public function category(): BelongsTo {
        return $this->belongsTo(Category::class, 'cat_id');
    }
}
