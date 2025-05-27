<?php

namespace App\Services;

use App\Models\MyCar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class MyCarService
{
    /**
     * 获取所有车辆列表 (可带分页)
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator|Collection
     */
    public function getAllCars(array $filters = [], int $perPage = 15)
    {
        $query = MyCar::query();

        // 示例：如果前端传入 user_id 用于过滤特定用户的车辆
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // 其他可能的过滤条件...

        if ($perPage > 0) {
            return $query->latest()->paginate($perPage);
        }
        return $query->latest()->get();
    }

    /**
     * 创建新的爱车记录
     *
     * @param array $data 验证过的数据
     * @return MyCar
     */
    public function createCar(array $data): MyCar
    {
        if (
            empty($data['user_id']) || 
            empty($data['name']) ||
            empty($data['phone']) || 
            empty($data['vin']) || 
            empty($data['licence_plate'])
        ) throw new Exception('用户ID、姓名、电话、车架号和车牌号号为必填项。');
        
        return DB::transaction(function () use ($data) {
            return MyCar::create($data);
        });
    }

    /**
     * 根据ID获取爱车详情
     *
     * @param int|string $id
     * @return MyCar|null
     */
    public function getCarById($id): ?MyCar
    {
        return MyCar::find($id);
    }

    /**
     * 更新爱车信息
     *
     * @param MyCar $myCar
     * @param array $data 验证过的数据
     * @return MyCar
     */
    public function updateCar(MyCar $myCar, array $data): MyCar
    {
        $myCar->update($data);
        return $myCar->refresh(); // 返回更新后的模型实例
    }

    /**
     * 删除爱车记录
     *
     * @param MyCar $myCar
     * @return bool|null
     */
    public function deleteCar(MyCar $myCar): ?bool
    {
        return $myCar->delete();
    }
}