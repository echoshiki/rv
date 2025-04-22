<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;

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
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\DateTimePicker::make('start_at')
                    ->label('生效开始时间')
                    ->native(false),
                Forms\Components\DateTimePicker::make('end_at')
                    ->label('生效结束时间')
                    ->native(false)
                    ->after('start_at'),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否启用')
                    ->default(true)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('图片预览')
                    // 确保预览时也用 public 盘
                    ->disk('public') 
                    // 设置预览宽度
                    ->width(100)
                    ->height('auto'),
                TextColumn::make('title')
                    ->label('标题')
                    ->limit(30)
                    // 鼠标悬停显示完整标题
                    ->tooltip(fn (Banner $record): string => $record->title ?? '')
                    ->searchable(),
                TextColumn::make('channelName')
                    ->label('所属频道')
                    ->badge()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // 自定义搜索逻辑，$channelName 是访问器
                        $channelKeys = array_keys(array_filter(
                            Banner::channelOptions(),
                            fn($value) => str_contains(strtolower($value), strtolower($search))
                        ));
                        return $query->whereIn('channel', $channelKeys);
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        // 因为 channelName 是访问器，需要告知如何排序
                        return $query->orderBy('channel', $direction);
                   }),
                IconColumn::make('is_active')
                   ->label('状态')
                   ->boolean()
                   ->sortable(),
                TextColumn::make('sort')
                    ->label('排序值')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('start_at')
                    ->label('开始时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                     // 默认隐藏，可切换显示    
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_date')
                    ->label('结束时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->label('按频道筛选')
                    ->options(Banner::channelOptions()),
                TernaryFilter::make('is_active')
                    ->label('按状态筛选')
                    ->boolean()
                    ->trueLabel('仅启用')
                    ->falseLabel('仅禁用')
                    ->native(false),
                Filter::make('effective_now')
                    ->label('当前生效')
                    ->query(function (Builder $query): Builder {
                        $now = now();
                        return $query
                                ->where('is_active', true)
                                ->where(function ($q) use ($now) {
                                    $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
                                 })
                                ->where(function ($q) use ($now) {
                                     $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
                                });
                    })
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
