<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.title', '微信小程序');
        $this->migrator->add('general.description', '微信小程序');
        $this->migrator->add('general.keywords', '微信小程序');
        $this->migrator->add('general.logo', 'origin/logo.png');
        $this->migrator->add('general.default_cover', 'origin/default_cover.jpg');
        $this->migrator->add('general.default_avatar', '');
        $this->migrator->add('general.email', 'info@laravel.com');
        $this->migrator->add('general.phone', '+123 456 7890');
        $this->migrator->add('general.address', '123 Main St, Anytown, USA');
        $this->migrator->add('general.copyright', 'Copyright © 2025 Laravel. All rights reserved.');
        $this->migrator->add('general.icp', '苏ICP备12345678号-1');
    }
};
