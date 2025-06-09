<?php

namespace App\Services;

use App\Models\User;
use App\Models\Rv;
use App\Models\RvOrder;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;

/**
 * 管理房车订单的业务逻辑
 */
class RvOrderService
{
    /**
     * 为用户创建一个新的房车订单
     * @param User $user 下单的用户
     * @param Rv $rv 被预定的房车
     * @return RvOrder 新创建的订单
     */
    public function createRvOrder(User $user, Rv $rv): RvOrder
    {
        // 执行验证逻辑，确保可以被预定
        if (!$rv->is_active) {
            throw new InvalidArgumentException('该房车当前不可预订。');
        }

        if ($rv->order_price <= 0) {
            throw new InvalidArgumentException('该房车定金价格配置错误。');
        }

        // 执行数据库事务
        return DB::transaction(function () use ($user, $rv) {
            return RvOrder::create([
                'user_id' => $user->id,
                'rv_id' => $rv->id,
                'order_no' => 'RV' . now()->format('YmdHisu') . Str::random(6),
                'deposit_amount' => $rv->order_price,
                'status' => OrderStatus::Pending,
            ]);
        });
    }

    /**
     * 获取用户的所有房车订单
     * @param User $user 用户
     * @return \Illuminate\Pagination\LengthAwarePaginator 订单列表
     */
    public function getUserRvOrders(
        User $user,
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10
    )
    {
        return RvOrder::where('user_id', $user->id)
            ->orderBy($orderBy, $sort)
            ->paginate($limit, ['*'], 'page', $page)->withQueryString();
    }

    /**
     * 获取用户房车订单详情
     * @param string $id 订单ID
     * @return RvOrder 订单详情
     */
    public function getRvOrderById(string $id)
    {
        return RvOrder::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
    }
}