<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

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

        if (!empty($filter['category_id'])) {
            $query->where('category_id', $filter['category_id']);
        }

        if (!empty($filter['search'])) {
            // 标题模糊搜索
            $searchText = $filter['search'];
            $query->where(fn($q) => $q->where('title', 'like', "%{$searchText}%")
                ->orWhere('content', 'like', "%{$searchText}%"));
        }

        $query->orderBy($orderBy, $sort);
        $query->paginate($limit, ['*'], 'page', $page);

        // 确保翻页时带上了除页码之外所有的参数
        return $query->withQueryString();
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

    // 创建文章
    public function createArticle(array $data): ?Article
    {
        try {
            // 开启事务
            return DB::transaction(function () use ($data) {
                // 创建文章主体
                $article = Article::create($data);
                return $article;
            });
        } catch (Throwable $e) {
            Log::error('创建文章时发生数据库错误:', [ /* ... */ ]);
            if (!empty($data['cover'])) {
                // 尝试删除可能已上传的文件
                Storage::disk('public')->delete($data['cover']);
            }
            return null;
        }
    }

    // 更新文章
    public function updateArticle(Article $article, array $data): ?Article
    {
        // 不允许修改作者
        unset($data['user_id']);

        try {
            // 开启事务
            return DB::transaction(function () use ($article, $data) {
                // 更新文章主体
                $article->update($data);
                return $article;
            });
        } catch (Throwable $e) {
            Log::error('更新文章时发生数据库错误:', [ /* ... */ ]);
            if (!empty($data['cover'])) {
                // 尝试删除可能已上传的文件
                Storage::disk('public')->delete($data['cover']);
            }
            return null;
        }
    }

    // 删除文章
    public function deleteArticle(Article $article): bool
    {
        try {
            return DB::transaction(function () use ($article) {
                $deleted = $article->delete();
                return $deleted;
            });
        } catch (Throwable $e) {
            Log::error("删除文章 [ID:{$article->id}] 时发生错误:", [ /* ... */ ]);
            return false;
        }
    }
}