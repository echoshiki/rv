<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    // 菜单图标
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = '系统设置';

    protected static ?string $navigationLabel = '轮播图管理';

    protected static ?string $label = '轮播图';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('标题')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->label('轮播图片')
                    ->image()
                    ->required()
                    // composer require joshembling/image-optimizer
                    // ->optimize('webp')
                    // 存储到 storage/app/public/banners 目录下
                    ->directory('banners') 
                    // 映射到 public 磁盘，需要执行 storage:link 命令
                    ->disk('public')
                    ->imageEditor()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('link')
                    ->label('跳转链接')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('channel')
                    ->label('所属频道')
                    // 从模型获取选项
                    ->options(Banner::channelOptions())
                    ->placeholder('请选择频道')
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('sort')
                    ->label('排序值')
                    ->helperText('数字越小越靠前显示')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否启用')
                    ->default(true)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('start_at')
                    ->label('生效开始时间')
                    ->native(false),
                Forms\Components\DateTimePicker::make('end_at')
                    ->label('生效结束时间')
                    ->native(false)
                    ->after('start_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('start_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
