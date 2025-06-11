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
                    'icon' =>  'origin/icons/form.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/registration/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '我的预定',
                    'icon' =>  'origin/icons/order.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/order/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '积分商城',
                    'icon' =>  'origin/icons/point.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/detail/index?code=points_mall',
                    'requires_auth' => true,
                ],
                [
                    'title' => '在线客服',
                    'icon' =>  'origin/icons/service.svg',
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
                    'icon' =>  'origin/icons/setting.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/user/profile/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '积分规则',
                    'icon' =>  'origin/icons/favorite.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/detail/index?code=points_rules',
                    'requires_auth' => false,
                ],
                [
                    'title' => '邀请好友',
                    'icon' =>  'origin/icons/invite.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/invite/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '隐私政策',
                    'icon' =>  'origin/icons/bill.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/detail/index?code=privacy_policy',
                    'requires_auth' => false,
                ],
                [
                    'title' => '用户协议',
                    'icon' =>  'origin/icons/agreement.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/detail/index?code=user_agreement',
                    'requires_auth' => false,
                ],
                [
                    'title' => '退出登录',
                    'icon' =>  'origin/icons/logout.svg',
                    'link_type' => 'function',
                    'link_value' => 'logout',
                    'requires_auth' => true,
                ],
            ]
        ],
        [
            'name' => '用车频道主菜单',
            'code' => 'usage_row_menu',
            'description' => '用车频道主菜单',
            'layout' => 'grid',
            'items' => [
                [
                    'title' => '我的房车',
                    'icon' =>  'origin/icons/usage/car.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/usage/car/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '维保预约',
                    'icon' =>  'origin/icons/usage/agreement.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/usage/maintenance/add/index',
                    'requires_auth' => true,
                ],
                [
                    'title' => '售后标准',
                    'icon' =>  'origin/icons/usage/handshake.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/detail/index?code=after_sales_standard',
                    'requires_auth' => false,
                ],
                [
                    'title' => '售后网点',
                    'icon' =>  'origin/icons/usage/net.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/detail/index?code=after_sales_network',
                    'requires_auth' => false,
                ],
                [
                    'title' => '水路使用',
                    'icon' =>  'origin/icons/usage/water.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/index?code=waterway_use',
                    'requires_auth' => false,
                ],
                [
                    'title' => '电路使用',
                    'icon' =>  'origin/icons/usage/electric.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/index?code=circuit_use',
                    'requires_auth' => false,
                ],
                [
                    'title' => '设备使用',
                    'icon' =>  'origin/icons/usage/equipment.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/index?code=equipment_use',
                    'requires_auth' => false,
                ],
                [
                    'title' => '仪表指示',
                    'icon' =>  'origin/icons/usage/dashboard.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/index?code=meter_indication',
                    'requires_auth' => false,
                ],
            ],
        ],
        [
            'name' => '用车频道悬浮菜单',
            'code' => 'usage_float_menu',
            'description' => '用车频道悬浮菜单',
            'layout' => 'grid',
            'items' => [
                [
                    'title' => '快速救援',
                    'icon' =>  'origin/icons/usage/float/network.svg',
                    'link_type' => 'page',
                    'link_value' => 'onMakePhoneCall',
                    'requires_auth' => false,
                ],
                [
                    'title' => '售后服务',
                    'icon' =>  'origin/icons/usage/float/help.svg',
                    'link_type' => 'function',
                    'link_value' => 'onMakePhoneCall',
                    'requires_auth' => false,
                ],
                [
                    'title' => '管家服务',
                    'icon' =>  'origin/icons/usage/float/service.svg',
                    'link_type' => 'function',
                    'link_value' => 'onOpenCustomerServiceChat',
                    'requires_auth' => false,
                ],
                [
                    'title' => '用户建议',
                    'icon' =>  'origin/icons/usage/float/message.svg',
                    'link_type' => 'page',
                    'link_value' => '/pages/usage/suggest/add/index',
                    'requires_auth' => true,
                ]
            ]
        ],
        [
            'name' => '首页矩阵图片菜单',
            'code' => 'home_matrix_menu',
            'description' => '首页矩阵图片菜单',
            'layout' => 'grid',
            'items' => [
                [
                    'title' => '企业简介',
                    'icon' =>  'origin/home/company.jpg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/detail/index?code=about_us',
                    'requires_auth' => false,
                ],
                [
                    'title' => '官方发布',
                    'icon' =>  'origin/home/publish.jpg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/index?code=official_release',
                    'requires_auth' => false,
                ],
                [
                    'title' => '行业资讯',
                    'icon' =>  'origin/home/industry.jpg',
                    'link_type' => 'page',
                    'link_value' => '/pages/article/index?code=industry_news',
                    'requires_auth' => false,
                ]
            ]
        ],
        [
            'name' => '首页标签栏菜单',
            'code' => 'home_tab_menu',
            'description' => '首页标签栏菜单',
            'layout' => 'grid',
            'items' => [
                [
                    'title' => '促销活动',
                    'icon' =>  null,
                    'link_type' => 'channel',
                    'link_value' => '3|activity',
                    'requires_auth' => false,
                ],
                [
                    'title' => '二手车',
                    'icon' =>  null,
                    'link_type' => 'channel',
                    'link_value' => '0|used_rv',
                    'requires_auth' => false,
                ],
                [
                    'title' => '车友活动',
                    'icon' =>  null,
                    'link_type' => 'channel',
                    'link_value' => '1|activity',
                    'requires_auth' => false,
                ]
            ]
        ],
        [
            'name' => '玩车频道标签栏菜单',
            'code' => 'activity_tab_menu',
            'description' => '玩车频道标签栏菜单',
            'layout' => 'grid',
            'items' => [
                [
                    'title' => '车友活动',
                    'icon' =>  null,
                    'link_type' => 'channel',
                    'link_value' => '1|activity',
                    'requires_auth' => false,
                ],
                [
                    'title' => '卫航营地',
                    'icon' =>  null,
                    'link_type' => 'channel',
                    'link_value' => '2|activity',
                    'requires_auth' => false,
                ]
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
