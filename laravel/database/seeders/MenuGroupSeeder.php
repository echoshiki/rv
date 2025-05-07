<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\MenuGroup;
use App\Models\MenuItem;

class MenuGroupSeeder extends Seeder
{
    const MENU_CODE_GROUP = [
        [
            'name' => '用户中心主菜单',
            'code' => 'user_row_menu',
            'description' => '用户中心横向带图标菜单',
            'layout' => 'grid',
            'items' => [
                [
                    'title' => '我的报名',
                    'icon' =>  'menu/icons/origin/form.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/activity/form/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '积分商城',
                    'icon' =>  'menu/icons/origin/point.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/point/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '积分规则',
                    'icon' =>  'menu/icons/origin/point-book.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/book/index',
                    'requires_auth' => false,
                ],
                [
                    'title' => '在线客服',
                    'icon' =>  'menu/icons/origin/service.svg',
                    'link_type' => 'function',
                    'link_value' => 'openCustomerServiceChat',
                    'requires_auth' => false,
                ],
            ]
        ],
        [
            'name' => '用户中心竖向菜单',
            'code' => 'user_column_menu',
            'description' => '用户中心竖向菜单',
            'layout' => 'vertical',
            'items' => [
                [
                    'title' => '个人资料',
                    'icon' =>  'menu/icons/origin/setting.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/user/profile/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '我的收藏',
                    'icon' =>  'menu/icons/origin/favorite.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/user/favorite/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '邀请好友',
                    'icon' =>  'menu/icons/origin/invite.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/book/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '隐私政策',
                    'icon' =>  'menu/icons/origin/bill.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/book/index',
                    'requires_auth' => false,
                ],
                [
                    'title' => '用户协议',
                    'icon' =>  'menu/icons/origin/agreement.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/book/index',
                    'requires_auth' => false,
                ],
                [
                    'title' => '退出登录',
                    'icon' =>  'menu/icons/origin/logout.svg',
                    'link_type' => 'function',
                    'link_value' => 'logout',
                    'requires_auth' => true,
                ],
            ]
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::MENU_CODE_GROUP as $group) {
            
            // 插入菜单组数据
            $record = MenuGroup::updateOrCreate(
                [
                    'code' => $group['code']
                ],
                [
                    'name' => $group['name'],
                    'description' => $group['description'],
                    'layout' => $group['layout']
                ]
            );

            // 成功后插入这个菜单组内的菜单项数据
            if ($record) {
                foreach ($group['items'] as $item) {
                    MenuItem::updateOrCreate(
                        [
                            'menu_group_id' => $record->id,
                            'title' => $item['title']
                        ],
                        [
                            'icon' =>  $item['icon'],
                            'link_type' => $item['link_type'],
                            'link_value' => $item['link_value'],
                            'requires_auth' => $item['requires_auth'], 
                        ]
                    );
                }
            }
        }
    }
}
