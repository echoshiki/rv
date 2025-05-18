<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ArticleDetailResource;
use App\Http\Resources\ArticleResourceCollection;

class ArticleController extends Controller
{
    protected $articleService;

    /**
     * 注入文章服务
     */
    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }
    
    /**
     * 获取文章列表
     */
    public function index(Request $request)
    {
        try {
            // 获取请求中的特定键值组成条件数组
            $filter = $request->only([
                'user_id',
                'is_active',
                'is_recommend',
                'category_id',
                'category_code',
                'search'
            ]);

            // 获取排序字段
            $orderBy = $request->get('orderBy', 'created_at');
            // 获取排序方式
            $sort = $request->get('sort', 'desc');
            // 获取当前页码
            $page = $request->get('page', 1);
            // 获取每页数据量
            $limit = $request->get('limit', 10);

            $articles = $this->articleService->getArticleList($filter, $orderBy, $sort, $page, $limit);

            // 使用 ArticleResourceCollection 包装分页结果
            return $this->successResponse(new ArticleResourceCollection($articles));

        } catch (\Throwable $e) {
            return $this->errorResponse('文章列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取文章详情
     */
    public function show(Request $request, int $id)
    {
        try {
            $article = $this->articleService->getArticleById($id);
            return $this->successResponse(new ArticleDetailResource($article));
        } catch (\Throwable $e) {
            return $this->errorResponse('文章详情获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 通过 code 获取单页详情
     */
    public function showByCode(Request $request, string $code)
    {
        try {
            $article = $this->articleService->getSinglePageByCode($code);
            return $this->successResponse(new ArticleDetailResource($article));
        } catch (\Throwable $e) {
            return $this->errorResponse('单页详情获取失败：' . $e->getMessage(), 500);
        }
    }
    
}
