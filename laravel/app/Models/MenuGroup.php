<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Seeders\MenuGroupSeeder;

class MenuGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'layout',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    /**
     * 获取该组所有的菜单项
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort');
    }

    /**
     * 获取活跃的菜单项
     */
    public function activeMenuItems(): HasMany
    {
        return $this->menuItems()->where('is_active', true);
    }

    /**
     * 获取所有受保护的菜单标识
     */
    public static function getProtectedCode(): array
    {
        return array_column(MenuGroupSeeder::MENU_CODE_GROUP, 'code');
    }

}
