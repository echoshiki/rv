<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    protected $menuService;
    
    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }
    
    /**
     * 获取指定slug的菜单组
     */
    public function getMenuGroup(Request $request, string $slug): JsonResponse
    {
        $menuGroup = $this->menuService->getMenuGroupBySlug($slug);
        
        if (!$menuGroup) {
            return response()->json([
                'success' => false,
                'message' => '未找到该菜单组',
            ], 404);
        }
        
        $menuItems = collect($menuGroup['menuItems']);
        $menuItems = $this->menuService->formatMenuItems($menuItems);
        
        return response()->json([
            'success' => true,
            'data' => [
                'menuGroup' => $menuGroup['menuGroup'],
                'menuItems' => $menuItems,
            ],
        ]);
    }
    
    /**
     * 获取所有活动菜单组
     */
    public function getAllMenuGroups(Request $request): JsonResponse
    {

        $menuGroups = $this->menuService->getAllActiveMenuGroups();
        
        $result = $menuGroups->map(function ($group) {
            $items = $this->menuService->formatMenuItems(collect($group->menuItems));     

            $groupData = $group->toArray();
            $groupData['menuItems'] = $items;
            
            return $groupData;
        });
        
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
