<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class PointLogService
{
    /**
     * 获取用户近一年的积分消耗记录
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserConsumptionLogsLastYear(
        User $user, 
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10
    ): LengthAwarePaginator
    {
        $oneYearAgo = Carbon::now()->subYear();

        return PointLog::where('user_id', $user->id)
            ->where('type', 'decrease')
            ->where('created_at', '>=', $oneYearAgo)
            ->orderBy($orderBy, $sort)
            ->paginate($limit, ['*'], 'page', $page)
            ->withQueryString();
    }

    /**
     * 获取用户近一年的积分记录
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserLogsLastYear(
        User $user, 
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10
    ): LengthAwarePaginator
    {
        $oneYearAgo = Carbon::now()->subYear();

        return PointLog::where('user_id', $user->id)
            ->where('created_at', '>=', $oneYearAgo)
            ->orderBy($orderBy, $sort)
            ->paginate($limit, ['*'], 'page', $page)
            ->withQueryString();
    }
}