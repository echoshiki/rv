<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleShowResource extends JsonResource
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
            'user_id' => $this->user_id, // 作者 ID
            'category_id' => $this->category_id, // 分类 ID
            'title' => $this->title,
            'content' => $this->content,
            'cover' => asset('storage/' . $this->cover), // 调用了模型中的 Accessor 获取封面 URL
            'sort' => $this->sort,
            'is_active' => (bool) $this->is_active, // 确保返回布尔值
            'published_at' => $this->published_at ? $this->published_at->toIso8601String() : null,
            // 包含关联关系，使用 whenLoaded 确保关系已被加载时才包含
            // 这样可以避免 N+1 问题，并且只在你需要时才加载关联数据
            'category' => $this->whenLoaded('category', function () {
                 // 如果 ArticleCategoryResource 存在，使用 ArticleCategoryResource 转换分类信息
                 if ($this->category) {
                    return new ArticleCategoryResource($this->category);
                 }
                 return null;
            })
        ];
    }
}
