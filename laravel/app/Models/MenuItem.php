<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_group_id',
        'title',
        'subtitle',
        'icon',
        'cover',
        'link_type',
        'link_value',
        'function_name',
        'requires_auth',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'requires_auth' => 'boolean',
        'is_active' => 'boolean',
        'sort' => 'integer'
    ];

    /**
     * 获取此菜单项所属的菜单组
     */
    public function menuGroup(): BelongsTo
    {
        return $this->belongsTo(MenuGroup::class);
    }

    /**
     * 获取链接类型列表
     */
    public static function getLinkTypes(): array
    {
        return [
            'page' => '小程序页面',
            'miniprogram' => '其他小程序',
            'webview' => '网页链接',
            'function' => '前端方法'
        ];
    }
}
