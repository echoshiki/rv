<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\MenuResource;

class MenuController extends Controller
{
    protected $menuService;
    
    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }
    
    /**
     * 获取指定code的菜单组
     */
    public function getMenuGroup(Request $request, string $code): JsonResponse
    {
        $menuGroup = $this->menuService->getMenuGroupByCode($code);
        
        if (!$menuGroup) {
            return $this->errorResponse('未找到菜单组', 404);
        }
        
        $menuItems = collect($menuGroup['menuItems']);

        return $this->successResponse(MenuResource::collection($menuItems));
    }
    
    /**
     * 获取所有活动菜单组
     */
    public function getAllMenuGroups(Request $request): JsonResponse
    {
        try {
            $menuGroups = $this->menuService->getAllActiveMenuGroups();

            $result = $menuGroups->map(function ($group) {
                $items = collect($group->menuItems);     

                $groupData = $group->toArray();
                $groupData['menuItems'] = $items;
                
                return $groupData;
            });

            return $this->successResponse($result);

        } catch (\Throwable $e) {
            return $this->errorResponse('菜单数据获取失败：' . $e->getMessage(), 500);
        }
    }
}
