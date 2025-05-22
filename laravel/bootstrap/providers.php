<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EasywechatServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    // 按环境变量加载 Telescope
    ...(!app()->environment('production') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class) 
        ? [\Laravel\Telescope\TelescopeServiceProvider::class] 
        : []),
];
