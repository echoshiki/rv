<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => mb_substr($this->name, 0, 12),
            'phone' => $this->maskPhoneNumber($this->phone),
            'avatar' => $this->avatar,
            'birthday' => $this->birthday ? $this->birthday->format('Y-m-d') : null,
            'sex' => $this->sex,
            'province' => $this->province,
            'city' => $this->city,
            'address' => $this->address,
            'level' => [
                'id' => $this->level ?? 1,
                'name' => User::getLevels()[$this->level] ?? '普通会员'
            ],
            'points' => $this->points
        ];
    }

    /**
     * 手机号码打码处理
     *
     * @param string|null $phoneNumber
     * @return string|null
     */
    protected function maskPhoneNumber(?string $phoneNumber): ?string
    {
        if (!$phoneNumber) {
            return null;
        }
        // 保留前三位和后四位，中间用星号替代
        return substr($phoneNumber, 0, 3) . '****' . substr($phoneNumber, -4);
    }


}
