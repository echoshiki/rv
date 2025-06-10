<?php

namespace Database\Factories;

use App\Models\ActivityRegistration;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Enums\RegistrationStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityRegistration>
 */
class ActivityRegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 确保 Activity 和 User 表中有数据，或者在调用此 Factory 前创建它们
        $activity = Activity::inRandomOrder()->first() ?? Activity::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $status = fake()->randomElement(RegistrationStatus::getValues());
        $fee = 0.00;

        // 如果状态是 approved，或者30%的几率已支付
        if ($status === RegistrationStatus::Approved->value || fake()->boolean(30)) {
            // 如果活动有注册费，则使用注册费，否则随机生成一个支付金额
            $fee = $activity->registration_fee > 0 ? $activity->registration_fee : fake()->randomFloat(2, 50, 300);
        }

        // 如果活动免费，则支付金额为0
        if ($activity->registration_fee == 0) { 
            $fee = 0.00;
        }

        $provinces = ['北京市', '上海市', '广东省', '江苏省', '浙江省', '四川省', '山东省'];
        $citiesByProvince = [
            '北京市' => ['北京市'],
            '上海市' => ['上海市'],
            '广东省' => ['广州市', '深圳市', '东莞市', '佛山市'],
            '江苏省' => ['南京市', '苏州市', '无锡市', '常州市'],
            '浙江省' => ['杭州市', '宁波市', '温州市', '绍兴市'],
            '四川省' => ['成都市', '绵阳市', '德阳市', '南充市'],
            '山东省' => ['济南市', '青岛市', '烟台市', '潍坊市'],
        ];
        $province = fake()->randomElement($provinces);
        $city = fake()->randomElement($citiesByProvince[$province]);

        return [
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'name' => fake()->name(),
            'phone' => fake()->unique()->phoneNumber(),
            'province' => $province,
            'city' => $city,
            'status' => $status,
            'fee' => $fee,
            'form_data' => json_encode([ // 示例表单数据
                'id_card' => fake()->optional(0.7)->ean13(), // 70% 几率有身份证号 (用ean13模拟)
                'emergency_contact_name' => fake()->optional(0.8)->name(),
                'emergency_contact_phone' => fake()->optional(0.8)->phoneNumber(),
                'vehicle_plate' => fake()->optional(0.5)->bothify('??#####'), // 50% 几率有车牌号
            ]),
            'admin_remarks' => fake()->optional(0.2)->sentence(), // 20% 几率有管理员备注
            'remarks' => fake()->optional(0.3)->paragraph(1),     // 30% 几率有用户备注
            'created_at' => fake()->dateTimeBetween($activity->published_at ?? $activity->created_at, 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * 生成已支付的报名记录
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function approved(): Factory
    {
        return $this->state(function (array $attributes, Factory $factory) {
            $activity = Activity::find($attributes['activity_id'] ?? Activity::factory()->create()->id);
            $fee = $activity->registration_fee > 0 ? $activity->registration_fee : fake()->randomFloat(2, 50, 300);

            return [
                'status' => RegistrationStatus::Approved->value,
                'fee' => $fee
            ];
        });
    }

    /**
     * 生成免费活动的报名记录
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forFreeActivity(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'fee' => 0.00,
            ];
        });
    }
}