<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MyCar;
use App\Models\User;

class MyCarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空
        MyCar::query()->delete();

        if (User::count() === 0) {
            echo "请先运行 UserSeeder 或确保 users 表中有数据。\n";
            return;
        }

        // 创建100条 MyCar 数据
        MyCar::factory()->count(100)->create();
    }
}
