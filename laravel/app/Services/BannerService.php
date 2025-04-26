<?php

namespace App\Services;

use App\Models\Banner; // 引入 Banner 模型
use Illuminate\Support\Facades\Cache; // 引入 Cache 门面
use Illuminate\Support\Collection; // 引入 Collection
use Illuminate\Support\Facades\Log; // 引入 Log 门面 (可选，用于记录错误)

class BannerService 
{
    /**
     * 缓存的默认时间（分钟）
     */
    protected int $cacheDuration = 10;

    /**
     * 获取指定频道下当前可用的轮播图列表
     *
     * @param string $channel 频道标识 (例如: 'home', 'vehicle')
     * @return Collection 返回包含轮播图数据的集合，每个元素包含 image_url 和 link_url
     */
    public function getActiveBannersByChannel(string $channel): Collection
    {
        // 定义缓存键
        $cacheKey = "banners_{$channel}_active";

        try {
            // 尝试从缓存获取，如果不存在则执行闭包逻辑并缓存结果
            $banners = Cache::remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use ($channel) {
                // 使用模型中定义的 scope 查询活跃且在有效期内的轮播图
                return Banner::activeForChannel($channel)
                        ->select('image', 'link') // 仅选择 API 需要的字段
                        ->get()
                        ->map(function ($banner) {
                            // 格式化数据：确保 image_path 是完整的 URL
                            // 注意：asset() 生成的 URL 基于 APP_URL，确保其在 .env 中配置正确
                            try {
                                // 如果 image_path 为空或无效，asset() 可能行为不确定，可以加判断
                                $imageUrl = $banner->image ? asset('storage/' . $banner->image) : null;
                            } catch (\Exception $e) {
                                // 处理 asset() 可能抛出的异常 (虽然少见)
                                Log::warning("Failed to generate asset URL for banner image: " . $banner->image, ['exception' => $e]);
                                $imageUrl = null; // 或返回默认图片 URL
                            }

                            return [
                                'image' => $imageUrl, // 返回格式化后的字段名
                                'link' => $banner->link,
                            ];
                        })
                        // 过滤掉图片 URL 生成失败的项 (可选)
                        ->filter(fn($banner) => $banner['image'] !== null);
            });

             // 确保返回的是 Collection 类型 (Cache::remember 可能返回 null 或其他类型，如果闭包执行失败且没有缓存)
            return $banners instanceof Collection ? $banners : collect([]);

        } catch (\Exception $e) {
            // 记录查询或缓存过程中可能发生的任何异常
            Log::error('Failed to retrieve banners for channel: ' . $channel, [
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString() // 可选，根据日志策略决定是否记录完整追踪
            ]);
            // 发生错误时返回空集合，避免API崩溃
            return collect([]);
        }
    }

    /**
     * 清除指定频道的轮播图缓存
     * (可以在后台更新轮播图时调用)
     *
     * @param string $channel
     * @return bool
     */
    public function clearChannelCache(string $channel): bool
    {
        $cacheKey = "banners_{$channel}_active";
        return Cache::forget($cacheKey);
    }

    /**
     * 清除所有轮播图频道的缓存
     * (可以在后台进行全局操作时调用)
     * @return void
     */
    public function clearAllChannelCache(): void
    {
        // 需要知道所有可能的 channel 值
        $channels = array_keys(Banner::channelOptions()); // 从模型获取所有频道 key
        foreach ($channels as $channel) {
            $this->clearChannelCache($channel);
        }
        // 或者如果频道不固定，可能需要更复杂的缓存标签策略
    }
}