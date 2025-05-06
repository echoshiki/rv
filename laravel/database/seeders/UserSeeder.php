<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\WechatUser;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 生成一个测试用的普通用户
        $wechatUser = User::updateOrCreate(
            [
                'email' => 'quo.maxime@example.net'
            ],
            [
                'name' => '微信用户7be3802bd6153cb2d4b859035d646237', 
                'password' => Hash::make('123123'),
                'email_verified_at' => now(),
                'phone' => '13218997189',
                'phone_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if ($wechatUser) {
            WechatUser::updateOrCreate(
                [
                    'openid' => 'oP0aB4uOgwqioYapbZSI_a42Yo74',     
                ],
                [
                    'user_id' => $wechatUser->id, 
                    'session_key' => 'eyJpdiI6IjlpbFpDOEFxSkI4MGtGZ2hKY3hsVVE9PSIsInZhbHVlIjoiRlkzeUxDOEZJT0RxQks2dzN0dU9FOXNlYmJ5SkNNanN0Q0IxUGpDY3BUdz0iLCJtYWMiOiI4NWU0N2U5NDJlMDUyMWE1ODY4NGJiM2U1M2Q0MDQ3ZjZlODY5YzlmMmI4YjkxY2ZkYTVhMjRiNWQ2ZTU3Y2U1IiwidGFnIjoiIn0',
                    'raw_data' => null,
                    'nickname' => null,
                    'avatar_url' => null,
                    'gender' => null,
                    'country' => null,
                    'province' => null,
                    'city' => null,
                    'deleted_at' => null
                ]
            );
            $this->command->info('成功生成测试用的微信账户。');
        }

        // 生成 10 个用户
        User::factory()->count(20)->create();
    }
}
