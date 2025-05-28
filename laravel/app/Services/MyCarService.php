<?php

namespace App\Services;

use App\Models\MyCar;
use Illuminate\Support\Facades\Auth;

class MyCarService
{
    public function createMyCar(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['vin'] = strtoupper($data['vin']);

        return MyCar::create($data);
    }

    public function getUserCars()
    {
        return MyCar::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCarById(int $id)
    {
        return MyCar::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
    }

    public function updateMyCar(MyCar $car, array $data)
    {
        // 处理车架号大写
        if (isset($data['vin']) && is_string($data['vin'])) {
            $data['vin'] = strtoupper($data['vin']);
        }
        
        return $car->update($data);
    }

    public function updateMyCarById(int $id, array $data)
    {
        $car = MyCar::find($id);
        if (!$car) {
            return false;
        }
        return $this->updateMyCar($car, $data);
    }
    
    public function deleteMyCar(MyCar $car)
    {
        return $car->delete();
    }

    /**
     * 验证车架号格式
     */
    public function validateVin(string $vin)
    {
        // 车架号通常是17位字符，不包含I、O、Q
        return preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', strtoupper($vin));
    }

    /**
     * 检查车架号是否已存在
     */
    public function vinExists(string $vin, int $excludeId)
    {
        $query = MyCar::where('vin', strtoupper($vin));
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}