<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ActivityCategory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{

     /**
     * 预定义的房车活动内容
     */
    private static array $articleContents = [
        '<p>探索未知，享受自由！加入我们的房车探险队，开启一段难忘的旅程。我们将穿越壮丽的山川，体验不同地域的风土人情。</p><p>本次活动提供全套房车设备租赁，并有专业领队指导，无论您是房车新手还是老玩家，都能轻松上手，尽享旅途乐趣。</p>',
        '<h2>房车生活体验周</h2><p>想要体验房车生活吗？本周末，我们将在风景优美的郊野公园举办房车生活体验活动。您可以参观各式房车，参与互动游戏，还有机会赢取房车租赁优惠券！</p>',
        '<h3>亲子房车露营嘉年华</h3><p>带上孩子，开上房车，一起来参加我们的亲子露营嘉年华吧！这里有丰富的亲子活动，篝火晚会，星空观测，让孩子们在自然中学习，在玩乐中成长。</p>',
        '<p><strong>限时优惠：</strong>凡报名参加本次长途房车旅行的家庭，均可享受九折优惠，并赠送价值500元的户外装备一套。</p><p>路线规划：城市A -> 风景区B -> 古镇C -> 城市A，全程约1200公里，预计行程7天。</p>',
    ];

    /**
     * 预定义的房车活动描述
     */
    private static array $articleDescriptions = [
        '一次说走就走的房车旅行，探索未知的美景，体验自由自在的生活方式。',
        '舟山群岛，海风拂面，驾驶房车，感受海岛的独特魅力，品尝新鲜的海鲜。',
        '年底清仓，多款热门房车超低折扣，购车即送大礼包，机会不容错过！',
        '亚洲顶级的房车展览盛会，汇聚全球知名品牌，最新车型抢先看，免费门票等你来领。',
        '参与房车文化交流，分享您的房车故事，与其他车友互动，还有机会获得精美纪念品。',
        '近距离感受房车的便捷与舒适，专业人士为您解答疑问，规划您的完美房车之旅。'
    ];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $articleTitles = [
            "十一黄金周，欢乐海南行！",
            "这个五一去哪里，舟山群岛自驾游",
            "年底房车大促，超值优惠等你来拿！",
            "开年上海房车展，现在开始领门票啦",
            "参加房车文化节，赢取精美礼品！",
            "线下房车活动，近距离体验房车魅力"  
        ];

        $title = fake()->randomElement($articleTitles);

        $categoryId = ActivityCategory::inRandomOrder()->first()?->id;

        $registrationStartAt = fake()->dateTimeBetween('-1 month', '+1 month');
        $registrationEndAt = fake()->dateTimeBetween($registrationStartAt, Carbon::instance($registrationStartAt)->addWeeks(4));
        $startedAt = fake()->dateTimeBetween($registrationEndAt, Carbon::instance($registrationEndAt)->addWeeks(2));
        $endedAt = fake()->dateTimeBetween($startedAt, Carbon::instance($startedAt)->addDays(fake()->numberBetween(1, 7)));
        $publishedAt = fake()->dateTimeBetween('-2 years', 'now'); // 80% 几率有发布时间

        $maxParticipants = fake()->optional(0.7)->numberBetween(50, 500); // 70% 几率设置最大人数
        $currentParticipants = $maxParticipants ? fake()->numberBetween(0, $maxParticipants) : 0;

        return [
            'user_id' => rand(1, 2),
            'category_id' => $categoryId,
            'title' => $title,
            'cover' => 'origin/default_cover.jpg', // 90% 几率有封面图
            'description' => fake()->randomElement(self::$articleDescriptions),
            'content' => fake()->randomElement(self::$articleContents),
            'registration_start_at' => $registrationStartAt,
            'registration_end_at' => $registrationEndAt,
            'registration_fee' => fake()->randomElement([0.00, fake()->randomFloat(2, 10, 500)]),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'max_participants' => $maxParticipants,
            'current_participants' => $currentParticipants,
            'code' => fake()->unique()->bothify('ACT-????####'), // 例如: ACT-ABCD1234
            'is_active' => fake()->boolean(90), // 90% 几率为 true
            'sort' => fake()->numberBetween(0, 100),
            'published_at' => $publishedAt,
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 years', 'now'),
        ];
    }

    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => Carbon::now()->subDays(rand(1, 30)), // 发布时间为过去30天内的某个时间
            ];
        });
    }

    public function upcoming(): Factory
    {
        return $this->state(function (array $attributes) {
            $registrationStartAt = Carbon::now()->addDays(rand(1, 15));
            $registrationEndAt = Carbon::instance($registrationStartAt)->addDays(rand(7, 20));
            $startedAt = Carbon::instance($registrationEndAt)->addDays(rand(1, 7));
            $endedAt = Carbon::instance($startedAt)->addDays(rand(1, 5));

            return [
                'registration_start_at' => $registrationStartAt,
                'registration_end_at' => $registrationEndAt,
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'published_at' => Carbon::now()->subHours(rand(1,24)), // 确保已发布
                'is_active' => true,
            ];
        });
    }

    public function ongoing(): Factory
    {
        return $this->state(function (array $attributes) {
            $startedAt = Carbon::now()->subDays(rand(0, 2)); // 活动已经开始0-2天
            $endedAt = Carbon::instance($startedAt)->addDays(rand(1, 5)); // 活动将在未来1-5天内结束
            $registrationEndAt = Carbon::instance($startedAt)->subDays(rand(1, 7));
            $registrationStartAt = Carbon::instance($registrationEndAt)->subDays(rand(7,14));


            return [
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'registration_start_at' => $registrationStartAt,
                'registration_end_at' => $registrationEndAt,
                'published_at' => Carbon::instance($registrationStartAt)->subDays(rand(1,5)), // 确保已发布
                'is_active' => true,
            ];
        });
    }

    public function ended(): Factory
    {
        return $this->state(function (array $attributes) {
            $endedAt = Carbon::now()->subDays(rand(1, 30)); // 活动已结束1-30天
            $startedAt = Carbon::instance($endedAt)->subDays(rand(1, 7));
            $registrationEndAt = Carbon::instance($startedAt)->subDays(rand(1,7));
            $registrationStartAt = Carbon::instance($registrationEndAt)->subDays(rand(7,14));

            return [
                'ended_at' => $endedAt,
                'started_at' => $startedAt,
                'registration_start_at' => $registrationStartAt,
                'registration_end_at' => $registrationEndAt,
                'published_at' => Carbon::instance($registrationStartAt)->subDays(rand(1,5)), // 确保已发布
                'is_active' => fake()->boolean(30), // 已结束的活动可能不再活跃
            ];
        });
    }
}
