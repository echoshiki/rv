<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserRequest;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    /**
     * 获取用户信息
     */
    public function index(Request $request)
    {
        try {
            return $this->successResponse(new UserResource($request->user()));
        } catch (\Throwable $e) {
            return $this->errorResponse('用户信息获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 更新用户信息
     */
    public function update(UserRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $request->user();
            $user->update($data);
            return $this->successResponse(new UserResource($user), '更新成功');
        } catch (\Throwable $e) {
            return $this->errorResponse('更新失败：' . $e->getMessage(), 500);
        }
    }
}
