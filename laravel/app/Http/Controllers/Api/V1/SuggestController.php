<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SuggestRequest;
use App\Services\SuggestService;

class SuggestController extends Controller
{
    protected SuggestService $suggestService;

    public function __construct(SuggestService $suggestService)
    {
        $this->suggestService = $suggestService;
    }

    public function store(SuggestRequest $request)
    {
        try {
            $data = $request->validated();
            $suggest = $this->suggestService->createSuggest($data);

            return $this->successResponse($suggest, '已成功添加用户建议。', 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('添加失败：' . $e->getMessage(), 500);
        }
    }
}
