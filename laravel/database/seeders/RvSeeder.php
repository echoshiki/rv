<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rv;

class RvSeeder extends Seeder
{
    const Rvs = [
        [
            'name' => '福特T8四驱',
            'cover' => 'origin/rv_cover_01.jpeg',
            'price' => 200000.00,
            'order_price' => 10.00,
            'content' => '<div class="attachment-gallery attachment-gallery--2"><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure></div>',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '福特T8',
            'cover' => 'origin/rv_cover_02.jpeg',
            'price' => 100000.00,
            'order_price' => 100.00,
            'content' => '<div class="attachment-gallery attachment-gallery--2"><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure></div>',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '福特T6',
            'cover' => 'origin/rv_cover_01.jpeg',
            'price' => 300000.00,
            'order_price' => 150.00,
            'content' => '<div class="attachment-gallery attachment-gallery--2"><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure></div>',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '福特JMC',
            'cover' => 'origin/rv_cover_02.jpeg',
            'price' => 450000.00,
            'order_price' => 150.00,
            'content' => '<div class="attachment-gallery attachment-gallery--2"><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure></div>',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '大通V90',
            'cover' => 'origin/rv_cover_01.jpeg',
            'price' => 300000.00,
            'order_price' => 150.00,
            'content' => '<div class="attachment-gallery attachment-gallery--2"><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure></div>',
            'is_active' => true,
            'sort' => 0
        ],
        [
            'name' => '依维柯欧胜',
            'cover' => 'origin/rv_cover_02.jpeg',
            'price' => 150000.00,
            'order_price' => 150.00,
            'content' => '<div class="attachment-gallery attachment-gallery--2"><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure><figure data-trix-attachment="{&quot;contentType&quot;:&quot;image/jpeg&quot;,&quot;filename&quot;:&quot;haibao.jpg&quot;,&quot;filesize&quot;:669233,&quot;height&quot;:2057,&quot;href&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;url&quot;:&quot;http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg&quot;,&quot;width&quot;:1200}" data-trix-content-type="image/jpeg" data-trix-attributes="{&quot;presentation&quot;:&quot;gallery&quot;}" class="attachment attachment--preview attachment--jpg"><a href="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg"><img src="http://weihang.yzypwl.com/storage/origin/rv_detail_image.jpg" width="1200" height="2057"><figcaption class="attachment__caption"><span class="attachment__name">haibao.jpg</span> <span class="attachment__size">653.55 KB</span></figcaption></a></figure></div>',
            'is_active' => true,
            'sort' => 0
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空房车表
        Rv::truncate();
        $this->command->info('开始生成测试用的房车数据...');
        Rv::insert(self::Rvs);
        $this->command->info('生成测试用的房车数据完毕');
    }
}
