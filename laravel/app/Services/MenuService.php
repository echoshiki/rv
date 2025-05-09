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
     * 根据用户认证状态过滤菜单项
     */
    public function filterMenuItemsByAuth(Collection $menuItems, bool $isAuthenticated): Collection
    {
        return $menuItems->filter(function ($item) use ($isAuthenticated) {
            return !$item->requires_auth || ($item->requires_auth && $isAuthenticated);
        });
    }
}
