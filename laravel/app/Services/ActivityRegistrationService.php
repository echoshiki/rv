<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\ActivityRegistration;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityRegistrationService
{
    protected ActivityService $activityService;

    public function __construct()
    {
        $this->activityService = app(ActivityService::class);
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

        // 1. 检查活动是否存在且有效
        if (!$activity || !$activity->is_active) {
            throw new Exception('活动不存在或未启用。');
        }

        // 2. 检查活动报名是否开放
        $now = now();
        if ($activity->registration_start_at && $activity->registration_start_at > $now) {
            throw new Exception('活动报名尚未开始。');
        }

        if ($activity->registration_end_at && $activity->registration_end_at < $now) {
            throw new Exception('活动报名已结束。');
        }

        // 3. 检查活动是否已满
        if (
            $activity->max_participants !== null && 
            $activity->max_participants > 0 &&
            $activity->current_participants >= $activity->max_participants
        ) {
            throw new Exception('活动报名人数已满。');
        }

        // 4. 检查用户是否已经报名过
        $existingActivityRegistration = ActivityRegistration::where('activity_id', $data['activity_id'])
            ->where('user_id', $data['user_id'])
            ->first();

        if ($existingActivityRegistration) {
            throw new Exception('您已经报名过该活动, 请勿重复提交。');
        }

        return DB::transaction(function () use ($data, $activity) {
            // 报名数据
            $registrationData = [
                'activity_id' => $data['activity_id'],
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'province' => $data['province'] ?? null,
                'city' => $data['city'] ?? null,
                'registration_no' => ActivityRegistration::generateUniqueRegistrationNo(),
                'status' => 'pending', 
                'paid_amount' => 0.00,
                'form_data' => json_encode($data),
                'remarks' => $data['remarks'] ?? null,
            ];

            // 如果活动是免费的，暂时直接设置为审核通过
            if ($activity->registration_fee == 0.00) {
                $registrationData['status'] = 'approved';
            }

            // 创建报名记录
            $registration = ActivityRegistration::create($registrationData);
            
            // 更新活动当前报名人数
            $activity->update([
                'current_participants' => $activity->current_participants + 1,
            ]);

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
        int $limit = 10
    ) {
        $query = ActivityRegistration::with(['activity' => function($q){
             // 选择活动需要的字段
            $q->select('id', 'title', 'cover', 'started_at', 'ended_at');
        }])->where('user_id', $userId);

        // 调用应用筛选的函数
        $this->applyCommonFilters($query, $filter);
        
        $query->orderBy($orderBy, $sort);

        return $query->paginate($limit, ['*'], 'page', $page)->withQueryString();
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
        $query = ActivityRegistration::with(['user' => function($q){
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
     * @param string $newStatus ['approved', 'rejected', 'cancelled']
     * @param string|null $adminRemarks 管理员备注
     * @return ActivityRegistration
     * @throws Exception
     */
    public function updateRegistrationStatus(
        int $registrationId, 
        string $newStatus, 
        ?string $adminRemarks = null
    ) {
        // 检索要更新状态的报名记录
        $registration = $this->getRegistrationById($registrationId);
        if (!$registration) {
            throw new Exception('报名记录不存在。');
        }

        // 确保状态是有效的
        $validStatuses = ['pending', 'approved', 'rejected', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception('无效的报名状态。');
        }

        // 获取当前状态
        $oldStatus = $registration->status;

        // 防止重复操作或无效状态转换
        if ($oldStatus === $newStatus) {
            return $registration; // 状态未改变
        }
        // 示例：已取消的不能再审批通过 (根据具体业务来定)
        // if ($oldStatus === 'cancelled' && $newStatus === 'approved') {
        //     throw new Exception('已取消的报名无法直接设置为通过。');
        // }

        return DB::transaction(function () use ($registration, $newStatus, $adminRemarks, $oldStatus) {
            $registration->status = $newStatus;
            if ($adminRemarks !== null) {
                $registration->admin_remarks = $adminRemarks;
            }
            $registration->save();

            // 根据状态变更，调整活动当前报名人数
            $activity = $registration->activity;
            if ($activity) {
                // 从非通过状态变为通过，且之前不是通过状态
                // 一般来说，只有从pending到approved才确认名额。
                // 如果创建时未增加，此处增加。
                // 如果创建时已增加，此处不处理，或在拒绝/取消时减少。
                // 当前的createRegistration已增加，此处逻辑需对应。
                if ($newStatus === 'approved' && $oldStatus !== 'approved') {
                    // $activity->increment('current_participants');
                }
                // 从通过状态变为非通过状态 (如取消或拒绝)
                elseif (($newStatus === 'cancelled' || $newStatus === 'rejected') && $oldStatus === 'approved') {
                    $activity->decrement('current_participants');
                }
                // 如果是从 pending 状态变为 rejected 或 cancelled，也应该减少 (因为 create 时增加了)
                elseif (($newStatus === 'cancelled' || $newStatus === 'rejected') && $oldStatus === 'pending') {
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
        int $registrationId, 
        int $userId, 
        ?string $remarks = null
    ) {
        // 获取需要取消的报名记录
        $registration = $this->getRegistrationById($registrationId);
        if (!$registration) {
            throw new Exception('报名记录不存在。');
        }

        // 检查是否是发起取消操作的用户
        if ($registration->user_id !== $userId) {
            throw new Exception('无权操作此报名记录。');
        }

        // 检查是否允许取消 (例如，活动开始前才允许)
        if ($registration->activity && $registration->activity->started_at && now()->isAfter($registration->activity->started_at)) {
            // throw new Exception('活动已开始，无法取消报名。'); // 根据业务决定
        }

        // 检查是否已经取消
        if ($registration->status === 'cancelled') {
            return $registration;
        }

        // 检查是否已经拒绝
        if ($registration->status === 'rejected' ) {
            throw new Exception('此报名已被处理，无法取消。');
        }

        return $this->updateRegistrationStatus($registrationId, 'cancelled', $remarks);
    }

    // 获取状态名称
    public function getStatusName(string $status): string
    {
        return ActivityRegistration::getStatuses()[$status] ?? '未知状态';
    }
    
}