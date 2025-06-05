<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'type',
        'amount',
        'points_before',
        'points_after',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        // 假设操作员也是 User 模型
        return $this->belongsTo(User::class, 'admin_id');
    }
}
