<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = '常规设置';

    protected static ?string $navigationGroup = '系统设置';

    protected static ?string $title = '常规设置';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(GeneralSettings::class);
        
        $this->form->fill([
            'title' => $settings->title,
            'description' => $settings->description,
            'keywords' => $settings->keywords,
            'logo' => $settings->logo,
            'default_cover' => $settings->default_cover,
            'default_avatar' => $settings->default_avatar,
            'copyright' => $settings->copyright,
            'email' => $settings->email,
            'phone' => $settings->phone,
            'address' => $settings->address,
            'icp' => $settings->icp,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('系统设置')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('基本信息')
                        ->icon('heroicon-m-adjustments-horizontal')
                            ->schema([
                                TextInput::make('title')
                                    ->label('系统名称')
                                    ->required(),
                                TextInput::make('description')
                                    ->label('系统描述')
                                    ->required(),
                                TextInput::make('keywords')
                                    ->label('关键词')
                                    ->required(),   
                                FileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->imagePreviewHeight('100')
                                    ->maxSize(1024),
                            ]),
                        Tabs\Tab::make('联系信息')
                            ->icon('heroicon-m-chat-bubble-left')
                            ->schema([
                                TextInput::make('email')
                                    ->label('邮箱')
                                    ->email(),
                                TextInput::make('phone')
                                    ->label('电话')
                                    ->required(),
                                TextInput::make('address')
                                    ->label('地址')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('其他设置')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                FileUpload::make('default_cover')
                                    ->label('默认封面')
                                    ->image()
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->imagePreviewHeight('100')
                                    ->maxSize(1024),
                                FileUpload::make('default_avatar')
                                    ->label('默认头像')
                                    ->image()
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->imagePreviewHeight('100')
                                    ->maxSize(1024),
                                TextInput::make('copyright')
                                    ->label('版权信息')
                                    ->required(),
                                TextInput::make('icp')
                                    ->label('备案号')
                                    ->required(),
                            ]),
                    ])
            ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $settings = app(GeneralSettings::class);
        $settings->title = $data['title'];
        $settings->description = $data['description'];
        $settings->keywords = $data['keywords'];
        $settings->logo = $data['logo'];  
        $settings->email = $data['email'];
        $settings->phone = $data['phone'];
        $settings->address = $data['address'];
        $settings->copyright = $data['copyright'];
        $settings->icp = $data['icp'];
        $settings->save();
        
        Notification::make()
            ->title('设置已保存')
            ->success()
            ->send();
    }
}
