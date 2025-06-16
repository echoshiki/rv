<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RvCategory;

class RvCategorySeeder extends Seeder
{
    const CATEGORY_CODE_GROUP = [
        [
            'code' => 'ford_t8_4wd',
            'title' => '福特T8四驱',
            'description' => '福特T8四驱'
        ],
        [
            'code' => 'ford_t8_long',
            'title' => '福特T8长高',
            'description' => '福特T8四驱'
        ],
        [
            'code' => 'ford_t8_mid',
            'title' => '福特T8中高',
            'description' => '福特T8四驱'
        ],
        [
            'code' => 'ford_t8',
            'title' => '福特T8',
            'description' => '福特T8四驱'
        ],
        [
            'code' => 'ford_t6_up',
            'title' => '福特T6升顶',
            'description' => '福特T8四驱'
        ],
        [
            'code' => 'ford_t6_mid',
            'title' => '福特T6中顶',
            'description' => '福特T8'
        ],
        [
            'code' => 'ford_t6',
            'title' => '福特T6',
            'description' => '福特T6'
        ],
        [
            'code' => 'jmc_long',
            'title' => '福顺JMC长高',
            'description' => '福顺JMC长高'
        ],
        [
            'code' => 'maxus_v90',
            'title' => '大通V90',
            'description' => '大通V90'
        ],
        [
            'code' => 'iveco_ousheng',
            'title' => '依维柯欧胜',
            'description' => '依维柯欧胜'
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //创建核心分类
        foreach(self::CATEGORY_CODE_GROUP as $item) {
            RvCategory::updateOrCreate(
                ['code' => $item['code']],
                [
                    'title' => $item['title'],
                    'description' => $item['description']
                ]
            );
        } 
    }
}
