<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UsedRv>
 */
class UsedRvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            "江苏福特JMC 2018年产 10万公里车况良好",
            "福特T8 2022年产 2万公里车况良好",
            "依维柯欧胜 2021年产 5万公里车况良好",
            "大通V90 2020年产 10万公里 配备家电",
            "福特T8 2022年产 9万公里 轮胎已换 无锡",
            "福特T6 北京 2013年产 10万公里车况良好"
        ];

        $name = fake()->randomElement($names);

        return [
            'name' => $name,
            'cover' => 'origin/rv_cover.jpg',
            'price' => fake()->randomFloat(2, 10, 500),
            'content' => '<div class="attachment-gallery attachment-gallery--2"><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure></div>',
            'is_active' => fake()->boolean(90),
            'sort' => fake()->numberBetween(0, 100)
        ];
    }
}
