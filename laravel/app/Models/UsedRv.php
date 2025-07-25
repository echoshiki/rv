<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedRv extends Model
{
    /** @use HasFactory<\Database\Factories\UsedRvFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'cover',
        'photos',
        'price',
        'order_price',
        'content',
        'is_active',
        'sort'
    ];

    // 类型转换
    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
        'price' => 'decimal:2',
        'order_price' => 'decimal:2',
        'photos' => 'array'
    ];

    // 获取相册
    public function getPhotosAttribute($value)
    {
        return json_decode($value, true);
    }

     // 数据创建时默认 content 的值
     protected static function booted()
     {
         static::saving(function ($model) {
             if (empty($model->content)) {
                 $model->content = '默认值'; 
             }
         });
     }
}
