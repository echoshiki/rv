<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\RvOrderService;
use App\Models\User;
use App\Models\Rv;
use App\Enums\OrderStatus;

class RvOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private RvOrderService $rvOrderService;

    // 初始化注入服务
    protected function setUp(): void
    {
        parent::setUp();
        $this->rvOrderService = new RvOrderService();
    }

    /**
     * 测试能否正常生成房车预订单
     */
    public function test_it_can_create_an_order_successfully(): void
    {
        // 创建一个测试用户 & 测试房车
        $user = User::factory()->create();
        $rv = Rv::factory()->create(['order_price' => 1000]);

        // 创建订单
        $order = $this->rvOrderService->createRvOrder($user, $rv);

        // 断言订单创建成功
        $this->assertNotNull($order);
        $this->assertInstanceOf(\App\Models\RvOrder::class, $order);

        // 验证数据是否已正确存入数据库
        $this->assertDatabaseHas('rv_orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'rv_id' => $rv->id,
            'deposit_amount' => 1000,
            'status' => OrderStatus::Pending->value,
        ]);
    }
}
