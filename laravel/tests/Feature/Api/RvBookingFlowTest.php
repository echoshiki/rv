<?php

namespace Tests\Features\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\User;
use App\Models\Rv;
use App\Models\RvOrder;
use App\Services\RvOrderService;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Model;
use App\Services\Wechat\PaymentService as WechatPaymentGateway;
use Mockery;
use Mockery\MockInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Event;
use App\Events\PaymentSucceeded;


class RvBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;
    protected $mockWechatGateway;
    protected User $user;
    protected Rv $rv;
    protected Model $payable;
    protected RvOrderService $rvOrderService;

    /**
     * 设置测试环境
     * 在每个测试方法执行前都会运行
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 创建测试用户和测试房车
        $this->user = User::factory()->create(); 
        $this->user->load('wechat_users');
        $this->user->wechatUser = $this->user->wechat_users()->first();

        $this->rv = Rv::factory()->create(['order_price' => 100]);

    }


    protected function tearDown(): void
    {
        // Mockery 清理
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_can_book_rv_and_initiate_payment_flow(): void
    {

        // 模拟返回支付参数的微信网关
        $this->mock(WechatPaymentGateway::class, function (MockInterface $mock) {
            // 捏造的返回数据
            $expectedFrontendParams = [
                'appId' => 'wx1234567890',
                'timeStamp' => '1234567890',
                'nonceStr' => 'random_string',
                'package' => 'prepay_id=wx12345',
                'signType' => 'RSA',
                'paySign' => 'signature_string'
            ];
            
            // 在这个闭包里为 $mock 对象定义期望
            $mock->shouldReceive('createJsApiTransaction')
                ->once()
                ->andReturn($expectedFrontendParams);
        });

        // ----------- 步骤一：用户调用创建房车订单接口 -----------

        $createOrderResponse = $this->actingAs($this->user)->postJson('/api/v1/rv-orders', [
            'rv_id' => $this->rv->id,
        ]);

        $createOrderResponse
            ->assertStatus(201)
            ->assertJsonPath('data.status.value', OrderStatus::Pending->value);

        // 获取订单 ID
        $orderId = $createOrderResponse->json('data.id');

        // ----------- 步骤二：用户调用创建支付单接口 -----------
        $initiatePaymentResponse = $this->actingAs($this->user)->postJson("/api/v1/payments/rv-orders/{$orderId}/pay");

        $initiatePaymentResponse
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.appId', 'wx1234567890');

        // ----------- 步骤三：验证数据库最终状态 -----------
        
        // 断言业务订单已正确创建
        $this->assertDatabaseHas('rv_orders', [
            'id' => $orderId,
            'user_id' => $this->user->id,
            'status' => OrderStatus::Pending->value,
        ]);
        
        // 断言支付单也已正确创建
        $this->assertDatabaseHas('payments', [
            'payable_id' => $orderId,
            'payable_type' => \App\Models\RvOrder::class,
            'status' => PaymentStatus::Pending->value,
            'amount' => $this->rv->order_price,
        ]);
    }

}
