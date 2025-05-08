<?php

namespace App\Services;

use App\Models\MenuGroup;
use Illuminate\Support\Collection;

class MenuService
{
    /**
     * 获取指定 code 的菜单组及其项目
     */
    public function getMenuGroupByCode(string $code, bool $onlyActive = true): ?MenuGroup
    {
        $query = MenuGroup::where('code', $code);
        
        if ($onlyActive) {
            $query->where('is_active', true);
        }
        
        $query->with(['menuItems' => function ($query) use ($onlyActive) {
            if ($onlyActive) {
                $query->where('is_active', true);
            }
            $query->orderBy('sort');
        }]);

        return $query->first();
    }
    
    /**
     * 获取所有可用的菜单组
     */
    public function getAllActiveMenuGroups(): Collection
    {
        return MenuGroup::where('is_active', true)
            ->orderBy('sort')
            ->with(['menuItems' => function ($query) {
                $query->where('is_active', true)->orderBy('sort');
            }])
            ->get();
    }
    
    /**
     * 格式化菜单项，处理图标URL等
     */
    public function formatMenuItems(Collection $menuItems): Collection
    {
        return $menuItems->map(function ($item) {
            // 处理图标URL
            if ($item->icon) {
                // 数据里增加图标真实路径参数
                $item->icon_url = asset('storage/' . $item->icon);
            }
            
            return $item;
        });
    }
    
    /**
     * 根据用户认证状态过滤菜单项
     */
    public function filterMenuItemsByAuth(Collection $menuItems, bool $isAuthenticated): Collection
    {
        return $menuItems->filter(function ($item) use ($isAuthenticated) {
            return !$item->requires_auth || ($item->requires_auth && $isAuthenticated);
        });
    }
}
