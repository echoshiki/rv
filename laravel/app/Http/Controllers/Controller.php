<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function successResponse($data = [], $message = '操作成功', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse($message = '系统异常', $code = 500, $data = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
    
}
