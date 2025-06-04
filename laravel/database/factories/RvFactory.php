<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RvCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class RvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 生成假标题
        $articleTitles = [
            '流浪地球5',
            '全顺T6小野兽',
            '全顺T8纵床款',
            '全顺T6穿山甲',
            '全顺T8黑豹',
            '江铃JMC大雪豹',
            '全顺T8四驱',
            '流浪地球4'
        ];
        $title = fake()->randomElement($articleTitles);

        $categoryId = RvCategory::inRandomOrder()->first()?->id;

        return [
            'category_id' => $categoryId,
            'name' => $title,
            'cover' => fake()->randomElement(['origin/rv_cover_01.jpeg', 'origin/rv_cover_02.jpeg']), 
            'photos' => ['origin/rv_cover_01.jpeg'],
            'price' => 200000.00,
            'order_price' => 10.00, 
            'content' => '<div class="attachment-gallery attachment-gallery--2"><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure></div>',
            'is_active' => true,
            'sort' => 0
        ];
    }
}
