<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string|null $title 标题
 * @property string $image 图片
 * @property string|null $link 跳转链接
 * @property string|null $channel 所属频道
 * @property int $sort 排序
 * @property bool $is_active 是否启用
 * @property \Illuminate\Support\Carbon|null $start_at 开始时间
 * @property \Illuminate\Support\Carbon|null $end_at 结束时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $channel_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner activeForChannel(string $channel)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Banner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Banner extends Model
{
    use HasFactory;

    // 定义频道（PHP 8.1+）
    public const CHANNEL_HOME = 'home';
    public const CHANNEL_ACTIVITY = 'activity';
    public const CHANNEL_POINTS_MALL = 'points_mall';

    // 定义频道名称映射
    public static function channelOptions(): array
    {
        return [
            self::CHANNEL_HOME => '首页',
            self::CHANNEL_ACTIVITY => '活动',
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
                          ->orWhere('start_at', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_at')
                          ->orWhere('end_at', '>=', $now);
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
