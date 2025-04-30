<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 检查摘要是否为空 (考虑 null 和空字符串，trim 去除首尾空格)
        if (blank($data['description'])) {
            // 确保内容字段存在且不为空
            if (!empty($data['content'])) {
                // 1. 去除内容中的 HTML 标签
                $content = $data['content'];
                // RichEditor 可能返回 HtmlString 对象
                $plainContent = strip_tags($content instanceof HtmlString ? $content->toHtml() : (string) $content);

                // 2. 截取前 N 个字符 (例如：150)，注意多字节字符安全
                $limit = 150; // 设置摘要长度限制
                $description = Str::limit($plainContent, $limit, '...'); // 使用 Laravel Str::limit 自动处理截断和省略号

                // 更新 $data 数组中的 description 字段
                $data['description'] = $description;
            }
        }

        // 必须返回修改后的 $data 数组
        return $data;
    }
}
