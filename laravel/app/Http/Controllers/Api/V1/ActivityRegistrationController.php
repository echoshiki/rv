<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ActivityRegistrationRequest;
use App\Http\Resources\RegistrationResource;
use App\Http\Resources\RegistrationResourceCollection;
use App\Http\Resources\RegistrationStatusResource;
use App\Services\ActivityService;
use App\Services\ActivityRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityRegistrationController extends Controller
{
    protected $activityService;
    protected $activityRegistrationService;

    public function __construct(
        ActivityService $activityService,
        ActivityRegistrationService $activityRegistrationService
    ) {
        $this->activityService = $activityService;
        $this->activityRegistrationService = $activityRegistrationService;
    }

    /**
     * 获取当前用户报名列表
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $filters = $request->only([
                'status', 
                'registration_no', 
                'activity_id', 
                'created_from', 
                'created_to', 
                'name', 
                'phone'
            ]);

            // 获取排序字段
            $orderBy = $request->get('orderBy', 'created_at');
            // 获取排序方式
            $sort = $request->get('sort', 'desc');
            // 获取当前页码
            $page = $request->get('page', 1);
            // 获取每页数据量
            $limit = $request->get('limit', 10);

            $registrations = $this->activityRegistrationService->getUserRegistrations(
                $user->id,
                $filters,
                $orderBy,
                $sort,
                $page,
                $limit
            );

            return $this->successResponse(new RegistrationResourceCollection($registrations));
        } catch (\Throwable $e) {
            return $this->errorResponse('报名列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 创建报名
     */
    public function store(ActivityRegistrationRequest $request)
    {
        try {
            // 通过创建的自定义请求验证校验数据
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            $registration = $this->activityRegistrationService->createRegistration($validatedData);

            return $this->successResponse(new RegistrationResource($registration), '报名成功！您的报名正在处理中。');
        } catch (\Throwable $e) {
            return $this->errorResponse('报名失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 报名详情
     */
    public function show(Request $request, int $id)
    {
        try {

            $registration = $this->activityRegistrationService->getRegistrationById($id);

            // 检索信息是否存在
            if (!$registration) {
                return $this->errorResponse('报名记录未找到。', 404);
            }

            // 只能查看自己的信息
            $user = $request->user();
            
            if ($registration->user_id !== $user->id) {
                return $this->errorResponse('无权查看此报名记录。', 404);
            }

            return $this->successResponse($registration);
        } catch (\Throwable $e) {
            return $this->errorResponse('报名详情获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 取消报名
     */
    public function cancel(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('未经授权。', 401);
            }

            $registration = $this->activityRegistrationService->userCancelRegistration(
                (int)$id,
                $user->id,
                $request->input('remarks')
            );

            return $this->successResponse($registration, '报名已成功取消。');
        } catch (\Throwable $e) {
            return $this->errorResponse('报名取消失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 支付记录
     */
    public function recordPayment(Request $request, $id) {}
}
