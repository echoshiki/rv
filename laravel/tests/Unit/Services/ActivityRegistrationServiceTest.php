<?php

namespace Tests\Unit\Services;

use App\Enums\RegistrationStatus;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\ActivityRegistrationService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Auth;

class ActivityRegistrationServiceTest extends TestCase
{
    use DatabaseTransactions; // 使用事务替代 RefreshDatabase

    private ActivityRegistrationService $registrationService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationService = $this->app->make(ActivityRegistrationService::class);
        $this->user = User::factory()->create();
    }

    //==============================================
    // 成功场景测试 (Happy Paths)
    //==============================================

    /**
     * 测试场景：成功创建一个付费活动的报名（状态应为待支付）。
     */
    #[Test]
    public function it_can_create_a_registration_for_a_paid_activity(): void
    {
        // 1. Arrange (准备)
        $activity = Activity::factory()->create([
            'registration_fee' => 100.00,
            'registration_start_at' => now()->subDays(1),
            'registration_end_at'   => now()->addDays(1),
            'is_active' => true,
            'max_participants' => 50,
            'current_participants' => 0,
        ]);
        
        $registrationData = $this->getRegistrationData($activity->id);

        // 2. Act (执行)
        $registration = $this->registrationService->createRegistration($registrationData);

        // 3. Assert (断言)
        $this->assertInstanceOf(ActivityRegistration::class, $registration);
        $this->assertEquals(RegistrationStatus::Pending, $registration->status);
        $this->assertEquals(100.00, $registration->fee);
        $this->assertEquals($this->user->id, $registration->user_id);
        $this->assertEquals($activity->id, $registration->activity_id);
        
        // 验证数据库记录
        $this->assertDatabaseHas('activity_registrations', [
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Pending->value,
            'fee' => 100.00,
            'name' => $registrationData['name'],
            'phone' => $registrationData['phone'],
        ]);
        
        // 验证活动参与人数已增加
        $activity->refresh();
        $this->assertEquals(1, $activity->current_participants);
    }

    /**
     * 测试场景：成功创建一个免费活动的报名（状态应为已通过）。
     */
    #[Test]
    public function it_can_create_an_approved_registration_for_a_free_activity(): void
    {
        // Arrange
        $activity = Activity::factory()->create([
            'registration_fee' => 0,
            'registration_start_at' => now()->subDays(1),
            'registration_end_at'   => now()->addDays(1),
            'is_active' => true,
            'max_participants' => null,
            'current_participants' => 0,
        ]);
        
        $registrationData = $this->getRegistrationData($activity->id);

        // Act
        $registration = $this->registrationService->createRegistration($registrationData);

        // Assert
        $this->assertInstanceOf(ActivityRegistration::class, $registration);
        $this->assertEquals(RegistrationStatus::Approved, $registration->status);
        $this->assertEquals(0, $registration->fee);
        
        $this->assertDatabaseHas('activity_registrations', [
            'activity_id' => $activity->id,
            'user_id' => $this->user->id,
            'status' => RegistrationStatus::Approved->value,
            'fee' => 0,
        ]);
        
        // 验证活动参与人数已增加
        $activity->refresh();
        $this->assertEquals(1, $activity->current_participants);
    }

    //==============================================
    // 失败/异常场景测试 (Unhappy Paths)
    //==============================================

    /**
     * 测试场景：缺少必填字段时应抛出异常
     */
    #[Test]
    public function it_throws_exception_when_required_fields_are_missing(): void
    {
        $activity = Activity::factory()->create(['is_active' => true]);
        
        // 测试缺少 activity_id
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('活动ID、用户ID、姓名和手机号为必填项。');
        
        $this->registrationService->createRegistration([
            'user_id' => $this->user->id,
            'name' => 'Test User',
            'phone' => '13800138000',
        ]);
    }

    /**
     * 测试场景：活动不存在时应抛出异常
     */
    #[Test]
    public function it_throws_exception_when_activity_does_not_exist(): void
    {
        $registrationData = $this->getRegistrationData(99999); // 不存在的活动ID
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('活动不存在或未启用。');
        
        $this->registrationService->createRegistration($registrationData);
    }

    /**
     * 测试场景：活动未启用时应抛出异常
     */
    #[Test]
    public function it_throws_exception_when_activity_is_not_active(): void
    {
        $activity = Activity::factory()->create(['is_active' => false]);
        $registrationData = $this->getRegistrationData($activity->id);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('活动不存在或未启用。');
        
        $this->registrationService->createRegistration($registrationData);
    }

    /**
     * 测试场景：活动报名尚未开始时，应抛出异常。
     */
    #[Test]
    public function it_throws_exception_if_registration_has_not_started(): void
    {
        // Arrange
        $activity = Activity::factory()->create([
            'registration_start_at' => now()->addDays(1),
            'registration_end_at'   => now()->addDays(2),
            'is_active' => true,
        ]);
        
        $registrationData = $this->getRegistrationData($activity->id);
        
        // 期望抛出异常
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('活动报名尚未开始。');

        // Act
        $this->registrationService->createRegistration($registrationData);
    }

    /**
     * 测试场景：活动报名已结束时，应抛出异常。
     */
    #[Test]
    public function it_throws_exception_if_registration_has_ended(): void
    {
        // Arrange
        $activity = Activity::factory()->create([
            'registration_start_at' => now()->subDays(2),
            'registration_end_at'   => now()->subDays(1),
            'is_active' => true,
        ]);

        $registrationData = $this->getRegistrationData($activity->id);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('活动报名已结束。');

        // Act
        $this->registrationService->createRegistration($registrationData);
    }

    /**
     * 测试场景：活动报名人数已满时应抛出异常
     */
    #[Test]
    public function it_throws_exception_when_activity_is_full(): void
    {
        $activity = Activity::factory()->create([
            'registration_fee' => 0,
            'registration_start_at' => now()->subDays(1),
            'registration_end_at'   => now()->addDays(1),
            'is_active' => true,
            'max_participants' => 2,
            'current_participants' => 2, // 已满
        ]);
        
        $registrationData = $this->getRegistrationData($activity->id);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('活动报名人数已满。');
        
        $this->registrationService->createRegistration($registrationData);
    }

    /**
     * 测试场景：用户重复报名时应抛出异常（待支付状态）
     */
    #[Test]
    public function it_throws_exception_when_user_has_pending_registration(): void
    {
        $activity = Activity::factory()->create([
            'registration_fee' => 100.00,
            'registration_start_at' => now()->subDays(1),
            'registration_end_at'   => now()->addDays(1),
            'is_active' => true,
        ]);
        
        // 创建一个待支付的报名记录
        ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Pending,
        ]);
        
        $registrationData = $this->getRegistrationData($activity->id);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('您有未支付的报名订单，请先完成支付或取消后重试。');
        
        $this->registrationService->createRegistration($registrationData);
    }

    /**
     * 测试场景：用户重复报名时应抛出异常（已通过状态）
     */
    #[Test]
    public function it_throws_exception_when_user_already_registered(): void
    {
        $activity = Activity::factory()->create([
            'registration_fee' => 0,
            'registration_start_at' => now()->subDays(1),
            'registration_end_at'   => now()->addDays(1),
            'is_active' => true,
        ]);
        
        // 创建一个已通过的报名记录
        ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Approved,
        ]);
        
        $registrationData = $this->getRegistrationData($activity->id);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('您已经报名过该活动, 请勿重复提交。');
        
        $this->registrationService->createRegistration($registrationData);
    }

    //==============================================
    // 查询方法测试
    //==============================================

    /**
     * 测试根据ID获取报名详情
     */
    #[Test]
    public function it_can_get_registration_by_id(): void
    {
        $activity = Activity::factory()->create();
        $registration = ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
        ]);
        
        $result = $this->registrationService->getRegistrationById($registration->id);
        
        $this->assertInstanceOf(ActivityRegistration::class, $result);
        $this->assertEquals($registration->id, $result->id);
        $this->assertTrue($result->relationLoaded('activity'));
        $this->assertTrue($result->relationLoaded('user'));
    }

    /**
     * 测试根据报名编号获取报名详情
     */
    #[Test]
    public function it_can_get_registration_by_number(): void
    {
        $activity = Activity::factory()->create();
        $registration = ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
            'registration_no' => 'REG123456',
        ]);
        
        $result = $this->registrationService->getRegistrationByNumber('REG123456');
        
        $this->assertInstanceOf(ActivityRegistration::class, $result);
        $this->assertEquals($registration->id, $result->id);
        $this->assertEquals('REG123456', $result->registration_no);
    }

    /**
     * 测试获取用户报名列表
     */
    #[Test]
    public function it_can_get_user_registrations(): void
    {
        $activity1 = Activity::factory()->create();
        $activity2 = Activity::factory()->create();
        
        // 创建该用户的报名记录
        ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity1->id,
            'status' => RegistrationStatus::Approved,
        ]);
        
        ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity2->id,
            'status' => RegistrationStatus::Pending,
        ]);
        
        // 创建其他用户的报名记录（不应该包含在结果中）
        $otherUser = User::factory()->create();
        ActivityRegistration::factory()->create([
            'user_id' => $otherUser->id,
            'activity_id' => $activity1->id,
        ]);
        
        $result = $this->registrationService->getUserRegistrations($this->user->id);
        
        $this->assertEquals(2, $result->total());
        $this->assertEquals(2, $result->count());
        
        // 测试状态筛选
        $approvedResult = $this->registrationService->getUserRegistrations(
            $this->user->id,
            ['status' => RegistrationStatus::Approved->value]
        );
        
        $this->assertEquals(1, $approvedResult->total());
    }

    /**
     * 测试获取活动报名列表
     */
    #[Test]
    public function it_can_get_activity_registrations(): void
    {
        $activity = Activity::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // 为该活动创建报名记录
        ActivityRegistration::factory()->create([
            'user_id' => $user1->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Approved,
        ]);
        
        ActivityRegistration::factory()->create([
            'user_id' => $user2->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Pending,
        ]);
        
        // 为其他活动创建报名记录（不应该包含在结果中）
        $otherActivity = Activity::factory()->create();
        ActivityRegistration::factory()->create([
            'user_id' => $user1->id,
            'activity_id' => $otherActivity->id,
        ]);
        
        $result = $this->registrationService->getActivityRegistrations($activity->id);
        
        $this->assertEquals(2, $result->total());
        $this->assertEquals(2, $result->count());
    }

    //==============================================
    // 状态更新测试
    //==============================================

    /**
     * 测试更新报名状态
     */
    #[Test]
    public function it_can_update_registration_status(): void
    {
        $activity = Activity::factory()->create(['current_participants' => 1]);
        $registration = ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Pending,
        ]);
        
        $updatedRegistration = $this->registrationService->updateRegistrationStatus(
            $registration,
            RegistrationStatus::Approved,
            '管理员通过'
        );
        
        $this->assertEquals(RegistrationStatus::Approved, $updatedRegistration->status);
        $this->assertEquals('管理员通过', $updatedRegistration->admin_remarks);
    }

    /**
     * 测试取消报名时参与人数减少
     */
    #[Test]
    public function it_decrements_participants_when_cancelling_approved_registration(): void
    {
        $activity = Activity::factory()->create(['current_participants' => 2]);
        $registration = ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Approved,
        ]);
        
        $this->registrationService->updateRegistrationStatus(
            $registration,
            RegistrationStatus::Cancelled
        );
        
        $activity->refresh();
        $this->assertEquals(1, $activity->current_participants);
    }

    /**
     * 测试用户取消报名
     */
    #[Test]
    public function it_can_user_cancel_registration(): void
    {
        Auth::login($this->user);
        
        $activity = Activity::factory()->create(['current_participants' => 1]);
        $registration = ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Approved,
        ]);
        
        $cancelledRegistration = $this->registrationService->userCancelRegistration(
            $registration,
            '用户主动取消'
        );
        
        $this->assertEquals(RegistrationStatus::Cancelled, $cancelledRegistration->status);
        
        // 验证参与人数减少
        $activity->refresh();
        $this->assertEquals(0, $activity->current_participants);
    }

    /**
     * 测试用户无权取消他人的报名
     */
    #[Test]
    public function it_throws_exception_when_user_cancels_others_registration(): void
    {
        Auth::login($this->user);
        
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create();
        $registration = ActivityRegistration::factory()->create([
            'user_id' => $otherUser->id,
            'activity_id' => $activity->id,
            'status' => RegistrationStatus::Approved,
        ]);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('无权操作此报名记录。');
        
        $this->registrationService->userCancelRegistration($registration);
    }

    /**
     * 测试查找用户在特定活动的报名记录
     */
    #[Test]
    public function it_can_find_user_registration_for_activity(): void
    {
        $activity = Activity::factory()->create();
        $registration = ActivityRegistration::factory()->create([
            'user_id' => $this->user->id,
            'activity_id' => $activity->id,
        ]);
        
        $result = $this->registrationService->findUserRegistrationForActivity(
            $this->user->id,
            $activity->id
        );
        
        $this->assertInstanceOf(ActivityRegistration::class, $result);
        $this->assertEquals($registration->id, $result->id);
    }

    /**
     * 测试未找到用户报名记录时返回 null
     */
    #[Test]
    public function it_returns_null_when_user_registration_not_found(): void
    {
        $activity = Activity::factory()->create();
        
        $result = $this->registrationService->findUserRegistrationForActivity(
            $this->user->id,
            $activity->id
        );
        
        $this->assertNull($result);
    }

    //==============================================
    // 辅助方法
    //==============================================

    /**
     * 获取测试用的报名数据
     */
    private function getRegistrationData(int $activityId): array
    {
        return [
            'activity_id' => $activityId,
            'user_id' => $this->user->id,
            'name' => $this->user->name ?? 'Test User',
            'phone' => '13800138000',
            'province' => '北京市',
            'city' => '北京市',
            'remarks' => 'Test registration',
        ];
    }
}