<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\BannerService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ManageCache extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = '系统设置';
    protected static ?string $navigationLabel = '缓存管理';
    protected static ?string $title = '缓存管理';
    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.pages.manage-cache';


    /**
     * 清除轮播图缓存
     */
    public function clearBannerCache(): void
    {
        try {
            $bannerService = app(BannerService::class);
            $bannerService->clearAllChannelCache();

            Notification::make()
                ->title('轮播图缓存已清除')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('轮播图缓存清除失败')
                ->danger()
                ->send();
        }
    }

    /**
     * 清除应用缓存
     */
    public function clearApplicationCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Notification::make()
                ->title('应用缓存已清除')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('应用缓存清除失败')
                ->danger()
                ->send();
        }
    }

}