<x-filament-panels::page>
    {{-- 你可以在这里添加一些说明文字或进一步的缓存管理界面 --}}
    <div class="space-y-4">
        <p>在此页面可以手动清除应用程序的各种缓存。</p>
        <p>请谨慎操作，清除缓存可能会短暂影响应用性能或需要用户重新登录（取决于清除的缓存类型）。</p>
    </div>
    <div class="flex flex-col gap-2">
        <x-filament::button
            class="w-1/3"
            icon="heroicon-o-photo"
            wire:click="clearBannerCache"
        >
            清除轮播图缓存
        </x-filament::button>
        <x-filament::button 
            class="w-1/3"
            icon="heroicon-o-cpu-chip"
            wire:click="clearApplicationCache"
        >
            清除应用缓存
        </x-filament::button>
    </div>
</x-filament-panels::page>