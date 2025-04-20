<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    // 定义频道（PHP 8.1+）
    public const CHANNEL_HOME = 'home';
    public const CHANNEL_ACTIVE = 'active';
    public const CHANNEL_POINTS_MALL = 'points_mall';

    // 定义频道名称映射
    public static function channelOptions(): array
    {
        return [
            self::CHANNEL_HOME => '首页',
            self::CHANNEL_ACTIVE => '活动',
            self::CHANNEL_POINTS_MALL => '积分商城'
        ];
    }

    // 可以被批量赋值的属性
    protected $fillable = [
        'title', 
        'image', 
        'link', 
        'channel', 
        'sort', 
        'is_active', 
        'start_at', 
        'end_at'
    ];

    // 类型转换
    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * 增加标准的查询作用域方便接口调用
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $channel
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveForChannel($query, string $channel)
    {
        $now = Carbon::now();
        return $query->where('channel', $channel)
                    ->where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('start_at')
                            ->where('start_at', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_at')
                            ->where('end_at', '>=', $now);
                    })
                    ->orderBy('sort', 'asc')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * 获取频道友好名称
     */
    public function getChannelNameAttribute()
    {
        return self::channelOptions()[$this->channel] ?? $this->channel;
    }
    
}
