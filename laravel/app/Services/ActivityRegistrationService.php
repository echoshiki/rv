<?php

namespace App\Services;

use App\Models\ActivityRegistration;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Enums\RegistrationStatus;
use Illuminate\Support\Facades\Auth;

class ActivityRegistrationService
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * 创建新的活动报名
     *
     * @param array $data 报名数据
     * @return ActivityRegistration
     * @throws Exception
     */
    public function createRegistration(array $data): ActivityRegistration
    {
        // 验证核心数据
        if (
            empty($data['activity_id']) ||
            empty($data['user_id']) ||
            empty($data['name']) ||
            empty($data['phone'])
        ) throw new Exception('活动ID、用户ID、姓名和手机号为必填项。');

        // 利用ActivityService获取活动详情
        $activity = $this->activityService->getActivityById($data['activity_id']);

        // 检查活动是否存在且有效
        if (!$activity || !$activity->is_active) {
            throw new Exception('活动不存在或未启用。');
        }

        // 检查活动报名是否开放
        $now = now();
        if ($activity->registration_start_at && $activity->registration_start_at > $now) {
            throw new Exception('活动报名尚未开始。');
        }

        if ($activity->registration_end_at && $activity->registration_end_at < $now) {
            throw new Exception('活动报名已结束。');
        }

        // 检查活动是否已满
        if (
            $activity->max_participants !== null &&
            $activity->max_participants > 0 &&
            $activity->current_participants >= $activity->max_participants
        ) {
            throw new Exception('活动报名人数已满。');
        }

        // 检查用户是否已经报名过
        $existingActivityRegistration = ActivityRegistration::where('activity_id', $data['activity_id'])
            ->where('user_id', $data['user_id'])
            ->first();

        if ($existingActivityRegistration) { 
            // 如果存在，再判断其状态
            if ($existingActivityRegistration->status === RegistrationStatus::Pending) {
                throw new Exception('您有未支付的报名订单，请先完成支付或取消后重试。');
            }
        
            // 如果是其他状态（如 Approved, Cancelled），则视为已报名过
            throw new Exception('您已经报名过该活动, 请勿重复提交。');
        }
  
        return DB::transaction(function () use ($data, $activity) {
            // 准备报名数据
            $registrationData = [
                'activity_id' => $data['activity_id'],
                'user_id'     => $data['user_id'],
                'name'        => $data['name'],
                'phone'       => $data['phone'],
                'province'    => $data['province'] ?? null,
                'city'        => $data['city'] ?? null,
                'fee'         => $activity->registration_fee, // ✅ 关键：记录下报名这一刻的费用
                'form_data'   => json_encode($data),
                'remarks'     => $data['remarks'] ?? null,
            ];

            if ($activity->registration_fee > 0) {
                $registrationData['status'] = RegistrationStatus::Pending;
            } else {
                $registrationData['status'] = RegistrationStatus::Approved;
            }

            // 创建报名记录
            $registration = ActivityRegistration::create($registrationData);

            // 更新活动当前报名人数
            // 是否支付成功后在占用名额？
            $activity->increment('current_participants');

            return $registration;
        });
    }

    /**
     * 根据ID获取报名详情
     *
     * @param int $id
     * @return ActivityRegistration|null
     */
    public function getRegistrationById(int $id): ?ActivityRegistration
    {
        return ActivityRegistration::with(['activity', 'user'])->find($id);
    }

    /**
     * 根据报名编号获取报名详情
     *
     * @param string $registrationNo
     * @return ActivityRegistration|null
     */
    public function getRegistrationByNumber(string $registrationNo): ?ActivityRegistration
    {
        return ActivityRegistration::with(['activity', 'user'])->where('registration_no', $registrationNo)->first();
    }

    /**
     * 获取指定用户的报名列表
     *
     * @param int $userId
     * @param array $filters
     * @param int $page
     * @param int $limit
     * @param string $orderBy
     * @param string $sort
     * @return LengthAwarePaginator
     */
    public function getUserRegistrations(
        int $userId,
        array $filter = [],
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10,
        array $fields = ['*']
    ) {
        $query = ActivityRegistration::with(['activity' => function ($q) {
            // 选择活动需要的字段
            $q->select('id', 'title', 'cover', 'started_at', 'ended_at');
        }])->where('user_id', $userId);

        // 调用应用筛选的函数
        $this->applyCommonFilters($query, $filter);

        $query->orderBy($orderBy, $sort);

        return $query->paginate($limit, $fields, 'page', $page)->withQueryString();
    }

    /**
     * 获取指定活动的报名列表
     *
     * @param int $activityId
     * @param array $filters
     * @param int $page
     * @param int $limit
     * @param string $orderBy
     * @param string $sort
     * @return LengthAwarePaginator
     */
    public function getActivityRegistrations(
        int $activityId,
        array $filters = [],
        int $page = 1,
        int $limit = 10,
        string $orderBy = 'created_at',
        string $sort = 'desc'
    ) {
        $query = ActivityRegistration::with(['user' => function ($q) {
            // 选择用户需要的字段，避免暴露过多信息
            $q->select('id', 'name');
        }])
            ->where('activity_id', $activityId);

        $this->applyCommonFilters($query, $filters);

        $query->orderBy($orderBy, $sort);
        return $query->paginate($limit, ['*'], 'page', $page)->withQueryString();
    }

    /**
     * 获取所有报名列表（后台管理用）
     */
    public function getAllRegistrations(
        array $filters = [],
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 15
    ) {
        $query = ActivityRegistration::with(['activity:id,title', 'user:id,name']);

        $this->applyCommonFilters($query, $filters);

        // 针对后台的额外筛选
        if (!empty($filters['activity_title'])) {
            $query->whereHas('activity', function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['activity_title'] . '%');
            });
        }
        if (!empty($filters['user_name'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['user_name'] . '%');
            });
        }

        $query->orderBy($orderBy, $sort);
        return $query->paginate($limit, ['*'], 'page', $page)->withQueryString();
    }

    /**
     * 应用通用筛选条件
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     */
    protected function applyCommonFilters($query, array $filter)
    {
        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }

        if (!empty($filter['registration_no'])) {
            $query->where('registration_no', $filter['registration_no']);
        }

        if (!empty($filter['name'])) {
            $query->where('name', $filter['name']);
        }

        if (!empty($filter['phone'])) {
            $query->where('phone', $filter['phone']);
        }

        if (!empty($filter['activity_id'])) {
            $query->where('activity_id', $filter['activity_id']);
        }

        // 根据创建时间筛选
        if (!empty($filter['created_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filter['created_from'])->startOfDay());
        }
        if (!empty($filter['created_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filter['created_to'])->endOfDay());
        }
    }

    /**
     * 更新报名状态
     *
     * @param int $registrationId
     * @param RegistrationStatus $newStatus 枚举类型的状态
     * @param string|null $adminRemarks 管理员备注
     * @return ActivityRegistration
     * @throws Exception
     */
    public function updateRegistrationStatus(
        ActivityRegistration $registration,
        RegistrationStatus $newStatus,
        ?string $adminRemarks = null
    ) {
        // 检索要更新状态的报名记录
        if (!$registration) {
            throw new Exception('报名记录不存在。');
        }

        // 获取当前状态（模型自动处理成了枚举类型）
        $oldStatus = $registration->status;

        // 防止重复操作或无效状态转换
        if ($oldStatus === $newStatus) {
            return $registration; // 状态未改变
        }

        return DB::transaction(function () use ($registration, $newStatus, $adminRemarks, $oldStatus) {
            // 更新报名状态，模型自动处理枚举类型
            $registration->status = $newStatus;
            
            // 更新管理员备注
            if ($adminRemarks !== null) {
                $registration->admin_remarks = $adminRemarks;
            }

            // 保存更新
            $registration->save();

            // 根据状态变更活动当前报名人数
            // 这里的实际情况需要结合活动模型创建时的人数逻辑来处理
            $activity = $registration->activity;
            if ($activity) {
                // 从非通过状态变为通过
                if ($newStatus === RegistrationStatus::Approved && $oldStatus !== RegistrationStatus::Approved) {
                    // $activity->increment('current_participants');
                }

                // 从通过状态变为非通过状态
                elseif (
                    ($newStatus === RegistrationStatus::Cancelled || $newStatus === RegistrationStatus::Rejected) 
                    && $oldStatus === RegistrationStatus::Approved
                ) {
                    $activity->decrement('current_participants');
                }

                // 从待处理状态变为非通过状态
                elseif (
                    ($newStatus === RegistrationStatus::Cancelled || $newStatus === RegistrationStatus::Rejected) 
                    && $oldStatus === RegistrationStatus::Pending
                ) {
                     $activity->decrement('current_participants');
                }
            }

            // TODO: 发送通知给用户等后续操作

            return $registration;
        });
    }

    /**
     * 用户取消报名
     *
     * @param int $registrationId
     * @param int $userId 发起取消操作的用户ID (用于权限验证)
     * @param string|null $remarks 用户备注
     * @return ActivityRegistration
     * @throws Exception
     */
    public function userCancelRegistration(
        ActivityRegistration $registration,
        ?string $remarks = null
    ) {
        // 获取需要取消的报名记录
        if (!$registration) {
            throw new Exception('报名记录不存在。');
        }

        // 检查是否是发起取消操作的用户
        if ($registration->user_id !== Auth::id()) {
            throw new Exception('无权操作此报名记录。');
        }

        // 检查是否已经取消
        if ($registration->status === RegistrationStatus::Cancelled) {
            return $registration;
        }

        // 检查是否已经拒绝
        if ($registration->status === RegistrationStatus::Rejected) {
            throw new Exception('此报名已被处理，无法取消。');
        }

        return $this->updateRegistrationStatus($registration, RegistrationStatus::Cancelled, $remarks);
    }

    /**
     * 根据用户ID和活动ID查找报名记录
     *
     * @param int $userId
     * @param int $activityId
     * @return ActivityRegistration|null
     */
    public function findUserRegistrationForActivity(int $userId, int $activityId): ?ActivityRegistration
    {
        return ActivityRegistration::where('user_id', $userId)
            ->where('activity_id', $activityId)
            ->first();
    }
}
