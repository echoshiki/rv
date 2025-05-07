<?php

namespace App\Services;

use App\Models\MenuGroup;
use Illuminate\Support\Collection;

class MenuService
{
    /**
     * 获取指定 code 的菜单组及其项目
     */
    public function getMenuGroupByCode(string $code, bool $onlyActive = true): ?array
    {
        $query = MenuGroup::where('code', $code);
        
        if ($onlyActive) {
            $query->where('is_active', true);
        }
        
        $menuGroup = $query->first();
        
        if (!$menuGroup) {
            return null;
        }
        
        $itemsQuery = $menuGroup->menuItems();
        
        if ($onlyActive) {
            $itemsQuery->where('is_active', true);
        }
        
        $items = $itemsQuery->orderBy('sort')->get();
        
        return [
            'menuGroup' => $menuGroup->toArray(),
            'menuItems' => $items->toArray(),
        ];
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
