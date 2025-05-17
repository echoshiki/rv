<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;         // 引入 Article 模型
use App\Models\ArticleCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{

     /**
     * 预定义的房车活动内容
     */
    private static array $articleContents = [
        "这个周末，我们一家人开着房车，进行了一次难忘的京郊探索之旅。从晨曦微露的清晨出发，一路向北，房车的便利让我们随时可以停下来欣赏沿途风光。孩子们在宽敞的车厢内嬉戏，不再有传统旅行的局促。傍晚时分，我们在一个风景如画的水库旁安营扎寨。自己动手烹饪晚餐，星空下围坐谈笑，这种与大自然亲密接触的体验，是城市生活无法比拟的。房车不仅仅是交通工具，更像是一个移动的家，承载着欢声笑语和温馨回忆。",
        "与三五好友相约，驾驶着各自的房车，来一场说走就走的周末露营，绝对是释放工作压力的最佳方式。我们选择了一个设施完善的房车营地，这里不仅有水电桩、排污口，还有公共淋浴间和烧烤区。白天，大家一起徒步、钓鱼，享受户外运动的乐趣；夜晚，点起篝火，弹起吉他，分享美食与故事。房车的存在，让露营变得既舒适又充满野趣，这种自由自在的感觉让人彻底放松。",
        "一直梦想着一次横跨数千公里的房车长途旅行，今年终于得以实现。我们从东部沿海出发，一路向西，穿越了繁华都市、广袤平原和壮丽山川。房车生活虽然充满未知与挑战，但也带来了无尽的惊喜。每天在不同的风景中醒来，品尝各地的特色美食，结识有趣的旅人。这种深度融入当地生活，随心所欲规划行程的旅行方式，赋予了“在路上”全新的意义。这不仅仅是一次旅行，更是一次对自我的探索和成长。",
        "对于初次接触房车旅行的朋友来说，一些实用的准备工作至关重要。首先，详细规划路线，预订合适的营地是成功的开始。其次，熟悉房车的水、电、气等各项设备的使用方法，确保旅途安全。别忘了检查车辆状况，备齐生活用品和应急工具。驾驶房车与普通小轿车有所不同，转弯、倒车时需更加小心。最重要的是，保持开放的心态，享受房车旅行为你带来的独特体验和沿途的风景。",
        "探索国内顶级的房车露营地，体验极致的户外奢华。从宁静的湖畔到壮丽的山谷，这些营地不仅提供完善的房车补给设施，还拥有独特的自然景观和丰富的休闲活动。想象一下，在清晨的鸟鸣中醒来，推开车门便是森林氧吧；傍晚在专属的露台烧烤，享受星空下的宁静。部分高端营地还提供温泉、儿童乐园等增值服务，让房车旅行不再是简单的餐风露宿，而是成为一种高品质的生活方式的延伸。",
    ];

    /**
     * 预定义的房车活动描述
     */
    private static array $articleDescriptions = [
        "记录一次全家房车京郊游，体验自然之美与家庭温馨，享受移动之家的乐趣。",
        "好友周末房车露营全攻略，包含营地选择、户外活动及夜晚篝火派对的精彩瞬间。",
        "数千公里房车长途探险记，从东到西，感受不同地域风情，体验在路上的自由与成长。",
        "房车新手上路必备指南：路线规划、设备检查、驾驶技巧及心态准备，助你轻松开启房车之旅。",
        "精选国内顶级房车露营地，享受户外奢华与自然风光的完美结合，开启高品质房车生活。",
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 生成假标题
        $articleTitles = [
            '房车京郊奇遇记：一家人的快乐时光',
            '周末不宅家！好友房车露营派对嗨翻天',
            '穿越中国东西：我的史诗级房车探险之旅',
            '房车小白必看：从零开始的房车旅行全攻略',
            '豪华房车营地大盘点：五星级的户外体验等你来',
        ];
        $title = fake()->randomElement($articleTitles);

        $categoryId = ArticleCategory::inRandomOrder()->first()?->id;

        return [
            'user_id' => rand(1, 2),
            'category_id' => $categoryId,
            'title' => $title,
            'content' => fake()->randomElement(self::$articleContents),
            'description' => fake()->randomElement(self::$articleDescriptions),
            'cover' => '', 
            'is_single_page' => fake()->boolean(10),
            'published_at' => fake()->dateTimeBetween('-2 years', 'now'), 
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 years', 'now'),
        ];
    }

    public function singlePage(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_single_page' => true,
            'category_id' => null, // 单页通常没有分类
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function regularArticles(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_single_page' => false,
            'cover' => 'origin/default_cover.jpg',
            // 确保有关联分类 ID (如果 factory 默认可能为 null)
            'category_id' => $attributes['category_id'] ?? ArticleCategory::inRandomOrder()->first()?->id,
        ]);
    }
}
