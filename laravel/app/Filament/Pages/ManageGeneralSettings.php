<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Section;
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
            'logo_path' => $settings->logo_path,
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
                // ...
                Section::make('基本信息')
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
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('logos')
                            ->visibility('public')
                            ->imagePreviewHeight('100')
                            ->maxSize(1024),
                    ]),

                Section::make('联系信息')
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
                
                Section::make('其他信息')
                    ->schema([
                        TextInput::make('copyright')
                            ->label('版权信息')
                            ->required(),
                        TextInput::make('icp')
                            ->label('备案号')
                            ->required(),
                    ]),
            ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $settings = app(GeneralSettings::class);
        $settings->title = $data['title'];
        $settings->description = $data['description'];
        $settings->keywords = $data['keywords'];
        $settings->logo_path = $data['logo_path'];  
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
