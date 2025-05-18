<?php

namespace App\Services;

use App\Models\Article;
use App\Models\SinglePage;

class ArticleService
{
    // 获取文章列表
    public function getArticleList(
        array $filter = [],
        string $orderBy = 'created_at',
        string $sort = 'desc',
        int $page = 1,
        int $limit = 10
    )
    {
        // 预加载作者和分类信息
        $query = Article::with(['category', 'user']);

        if (!empty($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        }

        if (!empty($filter['is_active'])) {
            $query->where('is_active', $filter['is_active']);
        } else {
            $query->active();
        }

        // 推荐
        if (!empty($filter['is_recommend'])) {
            $query->recommend();
        }

        if (!empty($filter['category_id'])) {
            $query->where('category_id', $filter['category_id']);
        }

        // 通过分类标识获取列表
        if (!empty($filter['category_code'])) {
            $query->whereHas('category', function ($q) use ($filter) {
                $q->where('code', $filter['category_code']);
            });
        }

        if (!empty($filter['search'])) {
            // 标题模糊搜索
            $searchText = $filter['search'];
            $query->where(fn($q) => $q->where('title', 'like', "%{$searchText}%")
                ->orWhere('content', 'like', "%{$searchText}%"));
        }

        $query->orderBy($orderBy, $sort);

        // 确保翻页时带上了除页码之外所有的参数
        return $query->paginate($limit, ['*'], 'page', $page)->withQueryString();
    }

    // 通过作者获取文章列表
    public function getArticleListByAuthor(int $authorId, int $page = 1, int $limit = 10)
    {
        return $this->getArticleList(['user_id' => $authorId], 'created_at', 'desc', $page, $limit);
    }

    // 通过分类获取文章列表
    public function getArticleListByCategory(int $categoryId, int $page = 1, int $limit = 10)
    {
        return $this->getArticleList(['category_id' => $categoryId], 'created_at', 'desc', $page, $limit);
    }

    // 通过分类标识获取文章列表
    public function getArticleListByCategoryCode(string $categoryCode, int $page = 1, int $limit = 10)
    {
        return $this->getArticleList(['category_code' => $categoryCode], 'published_at', 'desc', $page, $limit);
    }

    // 获取文章详情
    public function getArticleById(int $id): Article
    {
        return Article::with(['category'])->findOrFail($id);
    }

    // 通过 code 获取单页详情
    public function getSinglePageByCode(string $code): SinglePage
    {
        return SinglePage::with(['category'])->where('code', $code)->firstOrFail();
    }
}