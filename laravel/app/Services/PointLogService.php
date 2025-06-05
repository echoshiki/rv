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
    public function getUserConsumptionLogsLastYear(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $oneYearAgo = Carbon::now()->subYear();

        return PointLog::where('user_id', $user->id)
            ->where('type', 'decrease') // Assuming 'decrease' means consumption
            ->where('created_at', '>=', $oneYearAgo)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 获取用户近一年的积分记录
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserLogsLastYear(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $oneYearAgo = Carbon::now()->subYear();

        return PointLog::where('user_id', $user->id)
            ->where('created_at', '>=', $oneYearAgo)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}