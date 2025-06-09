<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use App\Models\Rv;
use App\Services\RvOrderService;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Model;
use App\Services\Wechat\PaymentService as WechatPaymentGateway;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;

class PaymentServiceTest extends TestCase
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

        // 创建 Mock 对象 - 模拟微信支付网关
        // 使用 Mockery 创建 WechatPaymentGateway 的模拟对象
        $this->mockWechatGateway = Mockery::mock(WechatPaymentGateway::class);

        // 创建 PaymentService 实例，注入模拟的网关
        $this->paymentService = new PaymentService($this->mockWechatGateway);

        $this->rvOrderService = new RvOrderService();

        // 创建测试用户和测试房车
        $this->user = User::factory()->create(); 
        $this->user->load('wechat_users');
        $this->user->wechatUser = $this->user->wechat_users()->first();

        $this->rv = Rv::factory()->create(['order_price' => 100]);

        // 创建测试用的支付对象 - 房车订单
        $this->payable = $this->rvOrderService->createRvOrder($this->user, $this->rv); 
    }


    protected function tearDown(): void
    {
        // Mockery 清理
        Mockery::close();
        parent::tearDown();
    }

    /**
     * 测试：成功创建 JSAPI 支付
     * 
     * 测试场景：
     * 1. 正常的支付创建流程
     * 2. 验证数据库事务
     * 3. 验证支付记录创建
     * 4. 验证微信网关调用
     */
    public function test_create_jsapi_payment_success()
    {
        // === 准备阶段 ===
        // 我们期望获得用于前端拉起支付的参数（假数据）
        $description = '测试订单支付';
        $expectedFrontendParams = [
            'appId' => 'wx1234567890',
            'timeStamp' => '1234567890',
            'nonceStr' => 'random_string',
            'package' => 'prepay_id=wx12345',
            'signType' => 'RSA',
            'paySign' => 'signature_string'
        ];

        // 获取实际的 payable 金额（从 deposit_amount 字段）
        $expectedAmountInCents = (int) ($this->payable->deposit_amount * 100);

        // 设置 Mock 期望 - 模拟微信网关返回成功的前端参数
        $this->mockWechatGateway
            ->shouldReceive('createJsApiTransaction')
            ->once()
            // 对传入的参数进行验证
            ->with(
                Mockery::pattern('/^PAY\d{20}\w{6}$/'), // 验证订单号格式：PAY + 11位时间戳 + 6位随机字符
                $expectedAmountInCents, // 使用实际的金额（分为单位）
                $description,
                $this->user->wechatUser->openid
            )
            // 返回捏造的数据
            ->andReturn($expectedFrontendParams); 

        // === 执行阶段 ===
        $result = $this->paymentService->createJsApiPayment(
            $this->payable,
            $this->user,
            $description
        );

        // === 验证阶段 ===
        
        // 1. 验证返回值
        $this->assertEquals($expectedFrontendParams, $result);

        // 2. 验证数据库中创建了支付记录
        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'payable_id' => $this->payable->id,
            'payable_type' => $this->payable->getMorphClass(),
            'amount' => $this->payable->deposit_amount,
            'status' => PaymentStatus::Pending,
        ]);

        // 3. 验证支付记录的详细信息
        $payment = Payment::first();
        $this->assertNotNull($payment);
        $this->assertMatchesRegularExpression('/^PAY\d{20}\w{6}$/', $payment->out_trade_no);
        $this->assertEquals(PaymentStatus::Pending, $payment->status);
    }

    /**
     * 测试：数据库事务回滚
     * 
     * 测试场景：
     * 当微信网关调用失败时，确保数据库事务能够正确回滚
     */
    public function test_create_jsapi_payment_transaction_rollback()
    {
        // === 准备阶段 ===
        $description = '测试订单支付';

        // 设置 Mock 期望 - 模拟微信网关抛出异常
        $this->mockWechatGateway
            ->shouldReceive('createJsApiTransaction')
            ->once()
            ->andThrow(new \Exception('微信支付网关错误'));

        // === 执行阶段 ===
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('微信支付网关错误');

        $this->paymentService->createJsApiPayment(
            $this->payable,
            $this->user,
            $description
        );

        // === 验证阶段 ===
        // 验证数据库中没有创建支付记录（事务回滚）
        $this->assertDatabaseMissing('payments', [
            'user_id' => $this->user->id,
            'payable_id' => $this->payable->id,
        ]);
    }

    /**
     * 测试：处理查询订单 - 支付成功场景
     * 
     * 测试场景：
     * 1. 处理微信查询接口返回的成功支付数据
     * 2. 验证支付记录状态更新
     * 3. 验证业务订单状态更新
     */
    public function test_process_queried_order_success()
    {
        // === 准备阶段 ===
        
        // 创建待处理的支付记录
        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'payable_id' => $this->payable->id,
            'payable_type' => $this->payable->getMorphClass(),
            'amount' => 100.00,
            'out_trade_no' => 'PAY20241209123456ABCDEF',
            'status' => PaymentStatus::Pending,
            'paid_at' => null,
            'transaction_id' => null,
        ]);

        // 模拟微信查询接口返回的成功数据
        $queriedOrderData = [
            'out_trade_no' => 'PAY20241209123456ABCDEF',
            'trade_state' => 'SUCCESS',
            'transaction_id' => 'wx_trans_123456789',
            'trade_state_desc' => '支付成功',
            'total_fee' => 10000,
            'time_end' => '20241209120000',
        ];

        // === 执行阶段 ===
        $result = $this->paymentService->processQueriedOrder($queriedOrderData);

        // === 验证阶段 ===
        
        // 1. 验证返回的支付记录
        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals($payment->id, $result->id);

        // 2. 验证支付记录状态更新
        $this->assertEquals(PaymentStatus::Paid, $result->status);
        $this->assertNotNull($result->paid_at);
        $this->assertEquals('wx_trans_123456789', $result->transaction_id);
        $this->assertEquals($queriedOrderData, $result->gateway_payload);

        // 3. 验证数据库中的支付记录
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::Paid,
            'transaction_id' => 'wx_trans_123456789',
        ]);

        // 4. 验证业务订单状态更新
        $this->payable->refresh();
        $this->assertEquals(OrderStatus::Paid, $this->payable->status);

    }

    /**
     * 测试：处理查询订单 - 支付失败场景
     * 
     * 测试场景：
     * 1. 处理微信查询接口返回的失败支付数据
     * 2. 验证支付记录状态更新为失败
     * 3. 验证业务订单状态不变
     */
    public function test_process_queried_order_failed()
    {
        // === 准备阶段 ===
        
        // 创建待处理的支付记录
        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'payable_id' => $this->payable->id,
            'payable_type' => $this->payable->getMorphClass(),
            'amount' => 100.00,
            'out_trade_no' => 'PAY20241209123456ABCDEF',
            'status' => PaymentStatus::Pending,
        ]);

        // 记录原始的业务订单状态
        $originalPayableStatus = $this->payable->status;

        // 模拟微信查询接口返回的失败数据
        $queriedOrderData = [
            'out_trade_no' => 'PAY20241209123456ABCDEF',
            'trade_state' => 'PAYERROR',
            'trade_state_desc' => '支付失败',
        ];

        // === 执行阶段 ===
        $result = $this->paymentService->processQueriedOrder($queriedOrderData);

        // === 验证阶段 ===
        
        // 1. 验证支付记录状态更新为失败
        $this->assertEquals(PaymentStatus::Failed, $result->status);
        $this->assertEquals($queriedOrderData, $result->gateway_payload);

         // 2. 验证数据库中的支付记录
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::Failed,
        ]);

        // 3. 验证业务订单状态没有变化
        $this->payable->refresh();
        $this->assertEquals($originalPayableStatus, $this->payable->status);
    }

    /**
     * 测试：处理查询订单 - 幂等性检查
     * 
     * 测试场景：
     * 1. 重复处理已经成功的支付订单
     * 2. 验证幂等性：不会重复更新
     * 3. 验证日志记录
     */
    public function test_process_queried_order_idempotency()
    {
         // === 准备阶段 ===
        
        // 创建已经支付成功的支付记录
        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'payable_id' => $this->payable->id,
            'payable_type' => $this->payable->getMorphClass(),
            'amount' => 100.00,
            'out_trade_no' => 'PAY20241209123456ABCDEF',
            'status' => PaymentStatus::Paid, // 已经是成功状态
            'paid_at' => Carbon::now()->subMinutes(10),
            'transaction_id' => 'wx_trans_original',
        ]);

        // 模拟微信查询接口返回的数据（尝试重复处理）
        $queriedOrderData = [
            'out_trade_no' => 'PAY20241209123456ABCDEF',
            'trade_state' => 'SUCCESS',
            'transaction_id' => 'wx_trans_new', // 不同的交易ID
        ];

        // 模拟日志
        Log::shouldReceive('warning')
            ->once()
            ->with('重复的支付通知或无效状态: PAY20241209123456ABCDEF');

        // === 执行阶段 ===
        $result = $this->paymentService->processQueriedOrder($queriedOrderData);

        // === 验证阶段 ===

        // 1. 验证返回原有的支付记录（没有新创建）
        $this->assertEquals($payment->id, $result->id);
        $this->assertEquals(PaymentStatus::Paid, $result->status);

        // 2. 验证支付记录没有被更新（保持原有的 transaction_id）
        $this->assertEquals('wx_trans_original', $result->transaction_id);
        $this->assertNotEquals('wx_trans_new', $result->transaction_id);

         // 3. 验证数据库中的记录没有变化
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::Paid,
            'transaction_id' => 'wx_trans_original',
        ]);
    }

    /**
     * 测试：处理查询订单 - 订单不存在
     * 
     * 测试场景：
     * 1. 处理不存在的订单号
     * 2. 验证抛出异常
     */
    public function test_process_queried_order_not_found()
    {
        // === 准备阶段 ===
        $queriedOrderData = [
            'out_trade_no' => 'NONEXISTENT_ORDER_12345',
            'trade_state' => 'SUCCESS',
            'transaction_id' => 'wx_trans_123456789',
        ];

        // === 执行阶段 & 验证阶段 ===
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->paymentService->processQueriedOrder($queriedOrderData);
    }

    /**
     * 测试：处理查询订单 - 数据库事务
     * 
     * 测试场景：
     * 1. 验证方法使用数据库事务
     * 2. 当更新业务订单失败时，支付记录也应该回滚
     */
    public function test_process_queried_order_transaction_rollback()
    {
        // === 准备阶段 ===
        
        // 创建待处理的支付记录
        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'payable_id' => $this->payable->id,
            'payable_type' => $this->payable->getMorphClass(),
            'amount' => 100.00,
            'out_trade_no' => 'PAY20241209123456ABCDEF',
            'status' => PaymentStatus::Pending,
        ]);

        // 模拟微信查询接口返回的成功数据
        $queriedOrderData = [
            'out_trade_no' => 'PAY20241209123456ABCDEF',
            'trade_state' => 'SUCCESS',
            'transaction_id' => 'wx_trans_123456789',
        ];

         // 模拟业务订单更新失败（通过删除 payable 记录）
        $this->payable->delete();

        // === 执行阶段 ===
        // 这里期望会抛出异常，因为 payable 不存在了
        $this->expectException(\Exception::class);

        $this->paymentService->processQueriedOrder($queriedOrderData);

        // === 验证阶段 ===
        // 验证支付记录状态没有被更新（事务回滚）
        $payment->refresh();
        $this->assertEquals(PaymentStatus::Pending, $payment->status);
        $this->assertNull($payment->transaction_id);
    }

    /**
     * 数据提供者：不同的支付金额测试
     * 
     * 用于测试不同金额的支付创建
     */
    public static function paymentAmountProvider()
    {
        return [
            '小额支付' => [0.01, 1],      // 1分钱
            '普通支付' => [100.00, 10000], // 100元
            '大额支付' => [9999.99, 999999], // 9999.99元
        ];
    }

    /**
     * 测试：不同金额的支付创建
     * 下方的注解，会让 PHPUnit 为每个数据集（paymentAmountProvider）运行一次测试方法
     * test_create_jsapi_payment_with_different_amounts(0.01, 1)
     * test_create_jsapi_payment_with_different_amounts(100.00, 10000)
     * test_create_jsapi_payment_with_different_amounts(9999.99, 999999)
     */
    #[DataProvider('paymentAmountProvider')]
    public function test_create_jsapi_payment_with_different_amounts($amount, $expectedCents)
    {
        // === 准备阶段 ===
        $this->payable->update(['deposit_amount' => $amount]);

        $this->mockWechatGateway
            ->shouldReceive('createJsApiTransaction')
            ->once()
            ->with(
                Mockery::any(),
                $expectedCents, // 验证转换为分的金额
                Mockery::any(),
                Mockery::any()
            )
            ->andReturn(['mock' => 'response']);
        
        // === 执行阶段 ===
        $this->paymentService->createJsApiPayment(
            $this->payable,
            $this->user,
            '测试支付'
        );

        // === 验证阶段 ===
        $this->assertDatabaseHas('payments', [
            'amount' => $amount,
        ]);
    }

}