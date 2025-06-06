<?php

namespace App\Services;

use App\Models\Maintenance;
use Illuminate\Support\Facades\Auth;

class MaintenanceService
{
    public function createMaintenance(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['issues'] = nl2br($data['issues']);
        return Maintenance::create($data);
    }

    public function getUserMaintenances()
    {
        return Maintenance::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMaintenanceById(int $id)
    {
        return Maintenance::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
    }

    public function updateMaintenance(Maintenance $maintenance, array $data)
    {        
        return $maintenance->update($data);
    }

    public function updateMaintenanceById(int $id, array $data)
    {
        $maintenance = Maintenance::find($id);
        if (!$maintenance) {
            return false;
        }
        return $this->updateMaintenance($maintenance, $data);
    }
    
    public function deleteMaintenance(Maintenance $maintenance)
    {
        return $maintenance->delete();
    }
}